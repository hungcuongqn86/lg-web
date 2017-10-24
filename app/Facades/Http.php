<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Http extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'http';
    }
}