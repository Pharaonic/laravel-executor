<?php

namespace Pharaonic\Laravel\Executor\Classes;

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
     * The file associated with the executor.
     *
     * @var SplFileInfo
     */
    public SplFileInfo $file;

    public function __construct(Executor $executor, SplFileInfo $file)
    {
        $this->executor = $executor;
        $this->file = $file;
    }

    /**
     * Get info about the executor.
     *
     * @return array
     */
    public function info()
    {
        return [
            'name' => basename($this->file->getFileName(), '.php'),
            'path' => $this->file->getRealPath(),

            'type' => $this->executor->type->name,
            'tags' => $this->executor->tags ?: null,
            'servers' => $this->executor->servers ?: null,
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
