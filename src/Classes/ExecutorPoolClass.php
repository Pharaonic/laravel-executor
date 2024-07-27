<?php

namespace Pharaonic\Laravel\Executor\Classes;

class ExecutorPoolClass
{
    private array $paths = [];

    public function __construct()
    {
        $this->paths = [
            base_path('executors'),
        ];
    }

    public function getPaths()
    {
        return $this->paths;
    }

    public function addPath($path)
    {
        array_push($this->paths, $path);
    }
}
