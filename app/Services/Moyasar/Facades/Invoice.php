<?php

namespace App\Services\Moyasar\Facades;

use Illuminate\Support\Facades\Facade;

class Invoice extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'moyasar.invoice';
    }
}
