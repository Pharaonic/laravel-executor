<?php

namespace Pharaonic\Laravel\Executor\Classes;

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
    protected ?Model $model = null;

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
     * Run the executor.
     *
     * @return void
     */
    public function run()
    {
        $this->executor->up();
    }

    /**
     * Reverse the executor.
     *
     * @return void
     */
    public function reverse()
    {
        $this->executor->down();
    }
}
