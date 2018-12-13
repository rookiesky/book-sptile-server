<?php
/**
 * Created by PhpStorm.
 * User: rookie
 * Date: 2018/12/11
 * Time: 18:05
 */

ini_set('display_errors','on');
date_default_timezone_set('PRC');

!defined('ROOT_PATH') && define('ROOT_PATH', str_replace("\\", "/", dirname(__FILE__)) . '/');

require 'function.php';

require 'vendor/autoload.php';

//sentry 抓取错误信息
//Raven_Autoloader::register();
//
//$sentryClient = new Raven_Client('https://f3204f748fc94064a1555225daef7fb5@sentry.io/1342528');
//
//$error_handler = new Raven_ErrorHandler($sentryClient);
//$error_handler->registerExceptionHandler();
//$error_handler->registerErrorHandler();
//$error_handler->registerShutdownFunction();

\App\Exceptions\Sentry::allErrorHandler();

//end sentry