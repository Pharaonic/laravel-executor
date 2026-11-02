<?php

namespace Pharaonic\Laravel\Executor\Console\Concerns;

use Pharaonic\Laravel\Executor\Classes\ExecutorLock;
use Throwable;

/**
 * @internal
 */
trait RunsWithLock
{
    /**
     * Run the callback while holding the executor lock, so servers deploying
     * at the same time run one after another.
     *
     * @param  callable(): mixed  $callback
     * @return int The command exit code.
     */
    protected function withLock(callable $callback): int
    {
        $seconds = config('pharaonic.executor.lock.seconds', 600);
        $seconds = is_numeric($seconds) ? (int) $seconds : 600;
        $lock = new ExecutorLock($seconds);

        try {
            $taken = $lock->block();
        } catch (Throwable $e) {
            $this->error("Couldn't take the executor lock: {$e->getMessage()}");

            return 1;
        }

        if (! $taken) {
            $this->error("Another server has been running the executors for more than {$seconds} seconds.");

            return 1;
        }

        try {
            $callback();
        } finally {
            $lock->release();
        }

        return 0;
    }
}
