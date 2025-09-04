<?php

namespace Pharaonic\Laravel\Executor\Classes;

class ExecutorManager
{
    /**
     * The executor pool instance.
     *
     * @var ExecutorPool
     */
    public ExecutorPool $pool;

    public function __construct()
    {
        $this->pool = new ExecutorPool();
    }
}
