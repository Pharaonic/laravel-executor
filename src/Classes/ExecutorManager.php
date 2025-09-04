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

    /**
     * Get info about all executors.
     *
     * @return array
     */
    public function info()
    {
        return $this->pool->collect()->info();
    }

    /**
     * Run all the executors.
     *
     * @return void
     */
    public function run()
    {
        //
    }
}
