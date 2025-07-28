<?php

namespace App\Services\Moyasar\Facades;

use Illuminate\Support\Facades\Facade;

class Payment extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'moyasar.payment';
    }
}
