<?php

namespace Pharaonic\Laravel\Executor\Classes;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use LogicException;
use Pharaonic\Laravel\Executor\Models\Executor as Model;

/**
 * @phpstan-import-type ExecutorInfo from ExecutorItem
 */
class ExecutorPool
{
    /**
     * These paths for all pools of executors.
     *
     * @var array<int, string>
     */
    protected array $paths = [];

    /**
     * All executors items.
     *
     * @var array<string, ExecutorItem>
     */
    protected array $items = [];

    public function __construct()
    {
        $this->paths = [base_path('executors')];
    }

    /**
     * Add a new path to executors pools.
     *
     * @return static
     */
    public function addPath(string $path)
    {
        array_push($this->paths, $path);

        return $this;
    }

    /**
     * Return all pools of executors.
     *
     * @return array<int, string>
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * Get info about all executors.
     *
     * @return array<string, ExecutorInfo>
     */
    public function info()
    {
        return array_map(fn (ExecutorItem $item) => $item->info(), $this->items);
    }

    /**
     * Collect all executors from the defined paths.
     *
     * @param  Collection<array-key, Model>  $records  The executor records keyed by name.
     * @return static
     */
    public function collect(Collection $records)
    {
        if (! empty($this->items)) {
            $this->items = [];
        }

        foreach ($this->paths as $path) {
            if (File::isDirectory($path) && ! File::isEmptyDirectory($path, true)) {
                foreach (File::files($path) as $file) {
                    if ($file->getExtension() === 'php') {
                        $obj = include $file->getRealPath();

                        if (! $obj instanceof Executor) {
                            continue;
                        }

                        $name = basename($file->getFileName(), '.php');
                        $record = $records->get($name);
                        $record = $record instanceof Model ? $record : null;

                        if (isset($this->items[$name])) {
                            throw new LogicException("Duplicate Executor name [$name] in file: ".$file->getRealPath());
                        }

                        $this->items[$name] = new ExecutorItem($obj, $file, $name, $record);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Get all collected executors items.
     *
     * @return array<string, ExecutorItem>
     */
    public function getItems()
    {
        return $this->items;
    }
}
