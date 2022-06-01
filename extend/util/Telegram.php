<?php
namespace util;

class Telegram
{

    public function access_msg($chat_id, $text)
    {
        $api = config('telegramApi');
        $apiUrl = $api['httpUrl'] . $api['accessKey'] . $api['url'];
        $data = array(
            "chat_id" => $chat_id,//"5165447029",
            "parse_mode" => "html",
            "text" => $text,//"<a href='https://baidu.com'>你好啊</a>",
        );
        $return = json_decode($this->CurlRequest($apiUrl, $data), true);
    }

}