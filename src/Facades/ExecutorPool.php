<?php

namespace Pharaonic\Laravel\Executor\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array getPaths()
 * @method static void addPath(string $path)
 */
class ExecutorPool extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'pharaonic.executor.executorPool';
    }
}
