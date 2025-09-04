<?php

namespace Pharaonic\Laravel\Executor\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array info()
 * @method static void run()
 */
class Executor extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'pharaonic.executor.manager';
    }
}
