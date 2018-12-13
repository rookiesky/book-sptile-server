<?php

namespace App\Models;


use Medoo\Medoo;

class Mysql
{
    static public function boot()
    {
        return new Medoo(config('databases'));
    }
}