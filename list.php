<?php

if(isset($_REQUEST['action']) && $_REQUEST['action'] != '') {

    require 'init.php';

    $bookList = new \App\Controllers\BookList();

    if($_REQUEST['action'] == 'list'){
        $bookList->boot();
    }elseif ($_REQUEST['action'] == 'list-check'){
        $bookList->check();
    }

}