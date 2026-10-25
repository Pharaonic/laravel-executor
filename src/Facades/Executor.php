<?php

namespace Pharaonic\Laravel\Executor\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Pharaonic\Laravel\Executor\Classes\ExecutorPool getPool()
 * @method static \Illuminate\Support\Collection<array-key, \Pharaonic\Laravel\Executor\Models\Executor> getRecords()
 * @method static int getNextBatchNumber()
 * @method static array<int, string> getIPs()
 * @method static array<string, array{name: string, path: string|false, type: \Pharaonic\Laravel\Executor\Enums\ExecutorType, tags: array<array-key, mixed>|null, servers: array<array-key, mixed>|null, batch: int|null, executed: bool}> info()
 *
 * @see \Pharaonic\Laravel\Executor\Classes\ExecutorManager
 */
class Executor extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'pharaonic.executor.manager';
    }
}
