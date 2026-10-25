<?php

namespace Pharaonic\Laravel\Executor\Classes;

use Illuminate\Support\Collection;
use Pharaonic\Laravel\Executor\Models\Executor;

/**
 * @phpstan-import-type ExecutorInfo from ExecutorItem
 */
class ExecutorManager
{
    /**
     * The executor pool instance.
     */
    protected ExecutorPool $pool;

    /**
     * The server IPs.
     *
     * @var array<int, string>
     */
    protected array $ips = [];

    public function __construct()
    {
        $this->pool = new ExecutorPool;
    }

    /**
     * Get the executor pool instance.
     *
     * @return ExecutorPool
     */
    public function getPool()
    {
        return $this->pool;
    }

    /**
     * Get all executor records, keyed by name.
     *
     * @return Collection<array-key, Executor>
     */
    public function getRecords()
    {
        return Executor::all()->keyBy('name');
    }

    /**
     * Get info about all executors.
     *
     * @return array<string, ExecutorInfo>
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
        $batch = Executor::query()->max('batch');

        return (is_numeric($batch) ? (int) $batch : 0) + 1;
    }

    /**
     * Get the server IPs.
     *
     * @return array<int, string>
     */
    public function getIPs()
    {
        if (! empty($this->ips)) {
            return $this->ips;
        }

        $addresses = dot(net_get_interfaces() ?: [])->get('*.unicast.*.address');

        return $this->ips = array_values(array_filter(
            is_array($addresses) ? $addresses : [],
            fn ($ip) => is_string($ip) && ! in_array($ip, ['127.0.0.1', '::1'], true)
        ));
    }
}
