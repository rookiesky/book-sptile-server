<?php
/**
 * Created by PhpStorm.
 * User: rookie
 * Date: 2018/12/11
 * Time: 18:04
 */

if(isset($_REQUEST['action']) && $_REQUEST['action'] != ''){
    require 'init.php';

    $website = 'https://www.zwdu.com';
    $prefix = '/book/';
    $id = 7554;
    $number = 1;

    $info = new \App\Controllers\BookInfo();

    if($_REQUEST['action'] == 'book'){

        $info->boot($website,$prefix,$id,$number);

    }elseif ($_REQUEST['action'] == 'book-check'){
        $info->check();
    }

}


