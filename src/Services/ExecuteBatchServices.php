<?php

namespace Pharaonic\Laravel\Executor\Services;

use Pharaonic\Laravel\Executor\Models\Executor;

final class ExecuteBatchServices
{
    protected function getLastBatch()
    {
        return Executor::latest('batch')->first()->batch ?? 0;
    }

    public function getCurrentBatch()
    {
        return $this->getLastBatch() + 1;
    }
}
