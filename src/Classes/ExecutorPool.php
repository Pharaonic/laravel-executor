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
     * Collect all executors from the defined paths.
     *
     * @return void
     */
    public function collect()
    {
        if (! empty($this->items)) {
            return;
        }

        foreach ($this->paths as $path) {
            if (File::isDirectory($path) && ! File::isEmptyDirectory($path, true)) {
                foreach (File::files($path) as $file) {
                    if ($file->getExtension() === 'php') {
                        $obj = include $file->getRealPath();

                        if (! $obj instanceof Executor) {
                            continue;
                        }

                        array_push($this->items, new ExecutorItem($obj, $file));
                    }
                }
            }
        }
    }
}
