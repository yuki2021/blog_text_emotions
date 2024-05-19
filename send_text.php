<?php

include('./conf/api_key.php');
require('./lib/get_db_data.php');
require('./lib/sentence_summary_chatgpt.php');

class ExecAbstratContent {
    
    private $DBObj;
    private $chatGPTObj;

    public function __construct() {
        $this->DBObj = new HandleDBData();
        $this->chatGPTObj = new SentenceSummaryChatGPT();
    }

    public function execSend() {
        $chatGPTObj = new SentenceSummaryChatGPT();

        $temp = $this->DBObj->getEmptyList();
        $i = 0;
        foreach($temp as $loop) {
            if($i >= 1) break;
            $temp_data = $this->DBObj->getData($loop->blog_id);
            if($temp_data === null) break;
            $text = $this->DBObj->trimStringData($temp_data->content);
            $this->chatGPTObj->setSendData($text);
            $this->chatGPTObj->execSendData();
            $formatData = $this->chatGPTObj->formatJSONdata();
            if($formatData !== false) {
                $this->DBObj->setAbstractData($loop->id, $formatData);
            } else {
                break;
            }
            sleep(1);
            $i++;
        }
    }
}

$obj = new ExecAbstratContent();
$obj->execSend();
