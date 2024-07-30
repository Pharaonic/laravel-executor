<?php

namespace Pharaonic\Laravel\Executor\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method array getPaths()
 * @method void addPath(string $path)
 */
class ExecutorPool extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'pharaonic.executor.executorPool';
    }
}
