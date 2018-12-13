<?php

namespace App\Exceptions;

class Sentry
{

    static public function client()
    {
        \Raven_Autoloader::register();
        return new \Raven_Client(config('system','sentry'));
    }

    /**
     * 自动捕获全局错误信息
     */
    static public function allErrorHandler()
    {
        $error_handler = new \Raven_ErrorHandler(self::client());
        $error_handler->registerExceptionHandler();
        $error_handler->registerErrorHandler();
        $error_handler->registerShutdownFunction();
    }

}