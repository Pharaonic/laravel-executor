<?php

namespace Pharaonic\Laravel\Executor\Classes;

class ExecutorItem
{
    public Executor $executor;
    public string $path;

    public function __construct(Executor $executor, string $path)
    {
        $this->executor = $executor;
        $this->path = $path;
    }

    /**
     * Get info about the executor.
     *
     * @return array
     */
    public function info()
    {
        return [
            'type'    => $this->executor->type,
            'tags'    => $this->executor->tags,
            'servers' => $this->executor->servers,
            'path'    => $this->path,
        ];
    }
}