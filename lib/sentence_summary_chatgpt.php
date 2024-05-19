<?php

include('./conf/api_key.php');

class SentenceSummaryChatGPT {

    private $header = array();
    private $send_data = array();
    private $get_data = "";

    // ChatGPT APIエンドポイント
    private $endpoint = 'https://api.openai.com/v1/chat/completions';


    public function __construct() {
        $this->setHeader();
    }

    private function setHeader() {
        global $chatGPT_api_key;
        $this->header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $chatGPT_api_key,
        );
    }

    public function setSendData($text) {
        
        $prompt = <<<EOT2
 #命令書:
 入力文を元に文章を要約してください。目的は300字程度の簡潔な文章にすることです。出力するのは要約文のみ。
 
 #入力文:
 {$text}
 
 #出力文:
EOT2;      
        
        $this->send_data = array(
            'model' => "gpt-3.5-turbo",
            'messages' => array(
                array(
                'role'=>'user',
                'content' => $prompt)
            )
        );
    }

    public function execSendData() {
        global $to_mail;
        global $from_mail;

        $maxRetries = 5;
        $retries = 0;
        $waitTime = 30;
    
        while ($retries < $maxRetries) {
            $context = array(
                'http' => array(
                    'method'  => 'POST',
                    'header'  => implode("\r\n", $this->header),
                    'content' => json_encode($this->send_data)
                )
            );
    
            $json = @file_get_contents($this->endpoint, false, stream_context_create($context));
            $responseCode = $this->parseHttpResponseCode($http_response_header);
    
            if ($responseCode == 200) {
                $this->get_data = $json;
                return;
            } else {
                $retries++;
                if ($responseCode == 429) {
                    $waitTime = 60; // Wait longer if there are too many requests
                }
                sleep($waitTime); // Wait before retrying
            }
        }
    
        $to = $to_mail;
        $subject = 'OpenAI API Error';
        $message = "Failed to get valid response from API after {$maxRetries} attempts";
        $headers = "From: {$from_mail}" . "\r\n";
        mb_send_mail($to, $subject, $message, $headers);

        throw new Exception($message);
    }

    public function formatJSONdata() {
        $tempHtml = '<ul>';
        $jsonArr = json_decode($this->get_data, true);
        if(empty($jsonArr)) {
            return false;
        }
        $tempHtml .= '<li>' . $jsonArr['choices'][0]['message']['content'] . '</li>';
                $tempHtml .= '</ul>';
        return $tempHtml;
    }

    private function parseHttpResponseCode($headers) {
        $statusLine = $headers[0];
        preg_match('{HTTP\/\S*\s(\d{3})}', $statusLine, $match);
        return intval($match[1]);
    }
}