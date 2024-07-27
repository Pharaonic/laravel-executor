<?php

namespace Pharaonic\Laravel\Executor\Facades;

use Illuminate\Support\Facades\Facade;

class ExecutorPool extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'pharaonic.executor.executorPool';
    }
}
