<?php

namespace Pharaonic\Laravel\Executor\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Pharaonic\Laravel\Executor\Classes\ExecutorPool getPool()
 * @method static \Illuminate\Support\Collection getRecords()
 * @method static int getNextBatchNumber()
 * @method static array getIPs()
 * @method static array info()
 */
class Executor extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'pharaonic.executor.manager';
    }
}
