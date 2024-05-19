<?php

require('./conf/db_config.php');
require('./vendor/autoload.php');

class HandleDBData {

    private $db;
    private $dbConfig;

    /// コンストラクタ
    public function __construct() {

        $this->dbConfig = $GLOBALS['dbConfig'];

        // DB接続クラス取得
        $this->db = new \Hadi\Database();
        $this->db->connect($this->dbConfig);
    }

    /// デストラクタ
    public function __destruct() {
        $this->db->disconnect();
    }

    /// データを一つだけ取得
    public function getData($hatena_blog_id) {
        $temp_arr = array();
        $temp_arr = $this->db->table('hatena_blog_data')->select([
            'field' => ['content'],
            'condition' => 'WHERE blog_id = "'. $hatena_blog_id .'"',
        ])->first();

        return $temp_arr;
    }

    /// まだhatena_content_abstractに保存されてないデータを一覧取得
    public function getEmptyList() {
        $tempArr = $this->db->query('SELECT a.id, a.blog_id FROM `hatena_blog_data` AS a 
        LEFT JOIN `hatena_content_abstract` AS b 
        ON a.`id` = b.`hatena_blog_id`
        where b.`abstract` IS NULL
        order by a.`published` desc')->get();

        return $tempArr;
    }

    /// 取得したデータをhatena_content_abstractに保存
    public function setAbstractData($hatena_blog_id, $abstract) {
        $this->db->table('hatena_content_abstract')->insert([
            'hatena_blog_id' => $hatena_blog_id,
            'abstract' => $abstract
        ]);
    }

    /// はてなブログのタグを削除
    public function trimStringData($string) {
        $string = preg_replace('/<[^>]*>/', '', $string);
        $string = preg_replace('/\[([^一-龠ぁ-んァ-ヴａ-ｚＡ-Ｚ０-９]*)\]/', '', $string);
        $string = str_replace(array("\r\n", "\r", "\n"), "", $string);
        $string = str_replace(array('　', ' '), '', $string);
        return $string;
    }
}