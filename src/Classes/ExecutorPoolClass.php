<?php

namespace Pharaonic\Laravel\Executor\Classes;

class ExecutorPoolClass
{
    /**
     * These paths for all pools of executors.
     */
    private array $paths = [];

    public function __construct()
    {
        $this->paths = [
            base_path('executors'),
        ];
    }

    /**
     * Return all pools of executors.
     * 
     * @return array
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * Add a new path to executors pools.
     * 
     * @param string $path
     * 
     * @return void
     */
    public function addPath(string $path): void
    {
        array_push($this->paths, $path);
    }
}
