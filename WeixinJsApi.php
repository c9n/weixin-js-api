<?php

class WeixinJsApi
{

    private $appid = '';

    private $appsecret = '';


    function __construct($appid, $appsecret)
    {
        $this->appid = $appid;
        $this->appsecret = $appsecret;
    }


    public function get_config($url)
    {
        $jsapi_ticket = $this->get_jsapi_ticket();
        $timestamp = time();
        $noncestr = $this->get_noncestr();
        $signature_key = sprintf('jsapi_ticket=%s&noncestr=%s&timestamp=%s&url=%s', $jsapi_ticket, $noncestr, $timestamp, $url);
        $signature = sha1($signature_key);

        return array(
            'appId' => $this->appid,
            'timestamp' => $timestamp,
            'nonceStr' => $noncestr,
            'signature' => $signature
        );
    }


    private function get_jsapi_ticket()
    {
        $ticket = $this->get_cache('jsapi_ticket');

        if ($ticket == null) {
            $ticket = $this->get_jsapi_ticket_by_api();
        }

        if ($ticket == null) {
            die("获取 jsapi_ticket 失败...\n");
        }

        return $ticket;
    }


    private function get_access_token()
    {
        $token = $this->get_cache('access_token');

        if ($token == null) {
            $token = $this->get_access_token_by_api();
        }

        if ($token == null) {
            die("获取 access_token 失败...\n");
        }

        return $token;
    }


    private function get_cache($name)
    {
        $cache = 'cache/' . $name;
        $token = file_get_contents($cache);

        // 提前一分钟更新
        if (time() - filemtime($cache) > 7140) {
            return null;
        }

        if (strlen($token) === 0) {
            return null;
        }

        return $token;
    }


    private function get_noncestr($length = 16)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $min = 1;
        $max = 10;

        return substr(str_shuffle(str_repeat($chars, mt_rand($min, $max))), 1, $length);
    }


    private function get_access_token_by_api()
    {
        $api = sprintf('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s', $this->appid, $this->appsecret);
        $resp = json_decode(file_get_contents($api), true);
        $token = '';

        if (array_key_exists('access_token', $resp)) {
            $token = $resp['access_token'];
        }

        file_put_contents('cache/access_token', $token);

        return $token;
    }


    private function get_jsapi_ticket_by_api()
    {
        $access_token = $this->get_access_token();

        $api = sprintf('https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=jsapi', $access_token);
        $resp = json_decode(file_get_contents($api), true);
        $ticket = '';

        if (array_key_exists('ticket', $resp)) {
            $ticket = $resp['ticket'];
        }

        file_put_contents('cache/jsapi_ticket', $ticket);

        return $ticket;
    }

}
