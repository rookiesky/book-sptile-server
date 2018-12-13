<?php
/**
 * Created by PhpStorm.
 * User: rookie
 * Date: 2018/12/11
 * Time: 20:17
 */

namespace App\Reptile;


class Regular
{
    static public function run($data, $pattern)
    {
        preg_match_all($pattern,$data,$result,PREG_SET_ORDER);
        return $result;
    }
}