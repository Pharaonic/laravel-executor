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
     * Get the executor records of this server, keyed by name: its own record
     * of an executor limited to servers, or else the shared record.
     *
     * @return Collection<array-key, Executor>
     */
    public function getRecords()
    {
        // Read from the write connection, so a replica that lags behind
        // never hides a record another server has just written.
        $query = Executor::query();
        $query->useWritePdo();
        $records = [];

        foreach ($query->get() as $record) {
            if ($record->server === null || $this->isServer($record->server)) {
                $records[] = $record;
            }
        }

        return collect($records)
            ->sortBy(fn (Executor $record) => $record->server === null ? 0 : 1)
            ->keyBy('name');
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
        $query = Executor::query();
        $query->useWritePdo();
        $batch = $query->max('batch');

        return (is_numeric($batch) ? (int) $batch : 0) + 1;
    }

    /**
     * Get the name of this server, from `pharaonic.executor.server`.
     *
     * @return string|null
     */
    public function getServer()
    {
        $server = config('pharaonic.executor.server');

        return is_string($server) && $server !== '' ? $server : null;
    }

    /**
     * Determine if a server name or IP address is this server.
     *
     * @return bool
     */
    public function isServer(string $server)
    {
        return $server === $this->getServer() || in_array($server, $this->getIPs(), true);
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
