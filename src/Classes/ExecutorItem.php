<?php

namespace Pharaonic\Laravel\Executor\Classes;

use Pharaonic\Laravel\Executor\Enums\ExecutorType;
use Pharaonic\Laravel\Executor\Facades\Executor as ExecutorFacade;
use Pharaonic\Laravel\Executor\Models\Executor as Model;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @internal
 *
 * @phpstan-type ExecutorInfo array{name: string, path: string|false, type: ExecutorType, tags: array<array-key, mixed>|null, servers: array<array-key, mixed>|null, batch: int|null, executed: bool}
 */
class ExecutorItem
{
    /**
     * The executor instance.
     */
    public Executor $executor;

    /**
     * The associated executor model.
     */
    public ?Model $model = null;

    /**
     * The name of the executor.
     */
    public string $name;

    /**
     * The file associated with the executor.
     *
     * @param  Executor  $executor
     * @param  SplFileInfo  $file
     * @param  string  $name
     * @param  Model|null  $model
     */
    public SplFileInfo $file;

    public function __construct(Executor $executor, SplFileInfo $file, string $name, ?Model $model = null)
    {
        $this->executor = $executor;
        $this->file = $file;
        $this->name = $name;
        $this->model = $model;
    }

    /**
     * Get info about the executor.
     *
     * @return ExecutorInfo
     */
    public function info()
    {
        return [
            'name' => $this->name,
            'path' => $this->file->getRealPath(),
            'type' => $this->executor->type,
            'tags' => $this->executor->tags ?: null,
            'servers' => $this->executor->servers ?: null,
            'batch' => $this->model?->batch,
            'executed' => $this->model?->executed > 0,
        ];
    }

    /**
     * Determine if the executor is executable.
     *
     * @return bool
     */
    public function isExecutable()
    {
        if ($servers = $this->executor->servers ?: null) {
            if (! array_filter(ExecutorFacade::getIPs(), fn ($ip) => in_array($ip, $servers))) {
                return false;
            }
        }

        if (! $this->model) {
            return true;
        }

        $this->model->fill([
            'type' => $this->executor->type,
            'tags' => $this->executor->tags ?: null,
            'servers' => $this->executor->servers ?: null,
        ]);

        return $this->model->isExecutable();
    }

    /**
     * Run the executor.
     *
     * @return void
     */
    public function run(int $nextBatch)
    {
        $this->executor->up();

        if (! $this->model) {
            $this->model = new Model([
                'type' => $this->executor->type,
                'name' => $this->name,
                'tags' => $this->executor->tags,
                'servers' => $this->executor->servers ?: null,
                'batch' => $nextBatch,
                'executed' => 1,
                'last_executed_at' => now(),
            ]);
            $this->model->save();
        } else {
            $this->model->execute();
        }
    }

    /**
     * Rollback the executor.
     *
     * @return void
     */
    public function rollback()
    {
        $this->executor->down();
    }
}
