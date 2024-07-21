<?php

namespace Pharaonic\Laravel\Executor\Contracts;

use Pharaonic\Laravel\Executor\Enums\ExecutorType;

abstract class ExecuteOnce
{
    public function getType()
    {
        return ExecutorType::Once;
    }
}
