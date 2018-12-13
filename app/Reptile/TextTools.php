<?php
/**
 * Created by PhpStorm.
 * User: rookie
 * Date: 2018/12/11
 * Time: 20:47
 */

namespace App\Reptile;

trait TextTools
{
    protected function gbkToUtf($text)
    {
        return mb_convert_encoding($text,'UTF-8','GBK');
    }
}