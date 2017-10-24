<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Lib extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'lib';
    }
}