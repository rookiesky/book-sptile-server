<?php
/**
 * Created by PhpStorm.
 * User: rookie
 * Date: 2018/12/11
 * Time: 18:16
 */

namespace App\Reptile\Http;


class Client
{
    public $http_code;

    public function get($url)
    {
        return $this->init($url);
    }

    private function init($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        //CURLOPT_FOLLOWLOCATION 跟踪网站302跳转
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        //CURLOPT_TIMEOUT 允许执行的最长秒数
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $data = curl_exec($ch);
        $this->http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $data;
    }
}