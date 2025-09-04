<?php

namespace Pharaonic\Laravel\Executor\Classes;

use Pharaonic\Laravel\Executor\Models\Executor;

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
     * Get all executor records.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getRecords()
    {
        return Executor::all()->keyBy('executor');
    }

    /**
     * Get info about all executors.
     *
     * @return array
     */
    public function info()
    {
        return $this->pool
            ->collect($this->getRecords())
            ->info();
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
