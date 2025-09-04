<?php

namespace Pharaonic\Laravel\Executor\Classes;

use Illuminate\Support\Facades\File;

class ExecutorPool
{
    /**
     * These paths for all pools of executors.
     *
     * @var array
     */
    protected array $paths = [];

    /**
     * All executors items.
     *
     * @var array
     */
    protected array $items = [];

    public function __construct()
    {
        $this->paths = [base_path('executors')];
    }

    /**
     * Add a new path to executors pools.
     *
     * @param string $path
     * @return void
     */
    public function addPath(string $path): void
    {
        array_push($this->paths, $path);
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
     * Return all executors paths from all pools.
     *
     * @return array
     */
    public function collect()
    {
        $list = [];
        
        foreach ($this->paths as $path) {
            if (File::isDirectory($path) && ! File::isEmptyDirectory($path, true)) {
                foreach (File::files($path) as $executor) {
                    if ($executor->getExtension() === 'php') {
                        array_push($list, $executor->getRealPath());
                    }
                }
            }
        }

        return $list;
    }
}
