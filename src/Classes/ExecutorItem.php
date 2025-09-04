<?php

namespace Pharaonic\Laravel\Executor\Classes;

use Pharaonic\Laravel\Executor\Facades\Executor as ExecutorFacade;
use Pharaonic\Laravel\Executor\Models\Executor as Model;
use Symfony\Component\Finder\SplFileInfo;

class ExecutorItem
{
    /**
     * The executor instance.
     *
     * @var Executor
     */
    public Executor $executor;

    /**
     * The associated executor model.
     *
     * @var Model|null
     */
    public ?Model $model = null;

    /**
     * The name of the executor.
     *
     * @var string
     */
    public string $name;

    /**
     * The file associated with the executor.
     *
     * @param Executor $executor
     * @param SplFileInfo $file
     * @param string $name
     * @param Model|null $model
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
     * @return array
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
        if (! $this->model) {
            return true;
        }

        if ($servers = $this->executor->servers ?: null) {
            if (! array_intersect($servers, ExecutorFacade::getIPs())) {
                return false;
            }
        }

        $this->model?->fill([
            'type' => $this->executor->type,
            'tags' => $this->executor->tags,
            'servers' => $this->executor->servers,
        ]);

        return $this->model?->isExecutable() ?? true;
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
            $this->model = Model::create([
                'type' => $this->executor->type,
                'name' => $this->name,
                'tags' => $this->executor->tags,
                'batch' => $nextBatch,
                'executed' => 1,
                'last_executed_at' => now(),
            ]);
        } else {
            $this->model->executed += 1;
            $this->model->last_executed_at = now();
            $this->model->save();
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
