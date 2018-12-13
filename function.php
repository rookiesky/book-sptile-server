<?php
if(!function_exists('config')){
    function config($file, $key = '', $default = null)
    {
        $data = include ROOT_PATH . 'config/' . $file . '.php';

        if($key == ''){
            return $data;
        }

        if(!isset($data[$key]) || empty($data[$key])){
            return $default;
        }

        return $data[$key];
    }
}