<?php

namespace Pharaonic\Laravel\Executor\Contracts;

use Pharaonic\Laravel\Executor\Enums\ExecutorType;

abstract class ExecuteAlways
{
    public function getType()
    {
        return ExecutorType::Always;
    }
}
