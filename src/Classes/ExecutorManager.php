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

    /**
     * The server IPs.
     *
     * @var array
     */
    protected array $ips = [];

    public function __construct()
    {
        $this->pool = new ExecutorPool();
    }

    /**
     * Get all executor records.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getRecords()
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
    * Get the next batch number.
    *
    * @return int
    */
    public function getNextBatchNumber()
    {
        return (Executor::orderBy('batch', 'desc')->first()?->batch ?? 0) + 1;
    }

    /**
     * Get the server IPs.
     *
     * @return array
     */
    public function getIPs()
    {
        if (! empty($this->ips)) {
            return $this->ips;
        }

        return $this->ips = array_filter(
            dot(net_get_interfaces())->get('*.unicast.*.address'),
            fn ($ip) => ! in_array($ip, ['127.0.0.1', '::1', null])
        );
    }
}
