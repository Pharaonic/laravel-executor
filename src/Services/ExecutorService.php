<?php

namespace Pharaonic\Laravel\Executor\Services;

use Illuminate\Support\Facades\File;
use Pharaonic\Laravel\Executor\Models\Executor;
use ReflectionClass;

final class ExecutorService
{
    /**
     * The dir of the executors.
     *
     * @var string
     */
    protected $dir;

    public function __construct()
    {
        $this->dir = base_path('executors');
    }

    /**
     * Check if the executors directory exists.
     *
     * @return bool
     */
    public function isExists()
    {
        return is_dir($this->dir);
    }

    /**
     * Get the executors.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function prepareExecutors()
    {
        $db = Executor::all()->keyBy('executor');

        return collect(array_map(function ($file) use ($db) {
            $class = new ReflectionClass(include $file);
            $name = basename($class->getFileName(), '.php');
            $executor = [
                'name' => $name,
                'type' => $class->getProperty('type')->getDefaultValue(),
                'tag' => $class->getProperty('tag')->getDefaultValue(),
                'path' => $class->getFileName(),
                'model' => $db[$name] ?? null
            ];

            if ($executor['model']) {
                $executor['model']->fill([
                    'type' => $executor['type'],
                    'tag' => $executor['tag'],
                ]);

                if ($executor['model']->isDirty()) {
                    $executor['model']->save();
                }
            }
            return $executor;
        }, File::glob($this->dir . '/*')))->keyBy('name');
    }


    /**
     * Sync the executors.
     *
     * @return \Illuminate\Support\Collection
     */
    public function sync()
    {
        $executors = $this->prepareExecutors();

        $newExecutors = $executors->filter(fn ($executor) => $executor['model'] == null);
        if ($newExecutors->isNotEmpty()) {
            $batch = $this->getNextBatch();

            $newExecutors->each(function ($executor) use (&$executors, $batch) {
                $executor['model'] = Executor::create([
                    'executor' => $executor['name'],
                    'type' => $executor['type'],
                    'tag' => $executor['tag'],
                    'batch' => $batch,
                ]);

                $executors->offsetSet($executor['name'], $executor);
            });
        }

        return $executors;
    }

    /**
     * Get the next batch number.
     *
     * @return int
     */
    protected function getNextBatch()
    {
        return (Executor::orderBy('batch', 'desc')->first()?->batch ?? 0) + 1;
    }
}
