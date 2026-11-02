<?php

namespace Pharaonic\Laravel\Executor\Classes;

use Illuminate\Database\Connection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * A lock kept as a reserved row of the executors table, shared by every
 * server that uses the table. The unique `name` and `server` pair lets only
 * one server insert the row; `executed` holds the owner and
 * `last_executed_at` the expiry.
 *
 * @internal
 */
class ExecutorLock
{
    /**
     * The name of the lock row, which the executor model never reads.
     */
    public const NAME = 'pharaonic:executor:lock';

    /**
     * The server of the lock row; not null, so the unique index applies.
     */
    protected const SERVER = '*';

    /**
     * The owner token of this lock.
     */
    protected int $owner;

    /**
     * @param  int  $seconds  How long the lock is held at most, and how long to wait for it.
     */
    public function __construct(protected int $seconds)
    {
        $this->owner = random_int(1, 2_147_483_647);
    }

    /**
     * Wait for the lock, up to the lock seconds.
     *
     * @return bool Whether the lock was taken.
     */
    public function block(): bool
    {
        $started = microtime(true);

        while (! $this->acquire()) {
            if (microtime(true) - $started >= $this->seconds) {
                return false;
            }

            usleep(250_000);
        }

        return true;
    }

    /**
     * Try to take the lock once: insert its row, or take over an expired one.
     */
    public function acquire(): bool
    {
        $now = now();
        $row = ['executed' => $this->owner, 'last_executed_at' => $now->copy()->addSeconds($this->seconds)];

        try {
            $this->query()->insert(['name' => self::NAME, 'server' => self::SERVER, 'batch' => 0] + $row);

            return true;
        } catch (QueryException) {
            // The row exists; it's free only when it has expired.
            return $this->row()->where('last_executed_at', '<=', $now)->update($row) === 1;
        }
    }

    /**
     * Release the lock when this instance holds it.
     */
    public function release(): void
    {
        $this->row()->where('executed', $this->owner)->delete();
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    protected function row()
    {
        return $this->query()->where('name', self::NAME)->where('server', self::SERVER);
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    protected function query()
    {
        $connection = config('pharaonic.executor.connection', config('database.default'));
        $table = config('pharaonic.executor.table', 'executors');

        /** @var Connection $db */
        $db = DB::connection(is_string($connection) ? $connection : null);

        return $db->table(is_string($table) ? $table : 'executors');
    }
}
