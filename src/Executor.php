<?php

namespace Pharaonic\Laravel\Executor;

use Illuminate\Console\OutputStyle;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Support\Facades\Artisan;
use Pharaonic\Laravel\Executor\Enums\ExecutorType;
use Pharaonic\Laravel\Executor\Traits\InteractsWithIO;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

abstract class Executor
{
    use InteractsWithIO;

    /**
     * The type of the executor.
     *
     * @var ExecutorType
     */
    public $type = ExecutorType::Always;

    /**
     * The tag of the executor.
     *
     * @var string|null
     */
    public $tag = null;

    /**
     * Execute it.
     *
     * @return void
     */
    abstract public function handle(): void;

    /**
     * Create a new Executor instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (! ($this->type instanceof ExecutorType)) {
            throw new \Exception('The type of the executor must be an instance of ExecutorType.');
        }

        if (! is_null($this->tag) && ! is_string($this->tag) && ! is_array($this->tag)) {
            throw new \Exception('The tag of the executor must be a string or an array or null.');
        }

        $this->output = new OutputStyle(new ArgvInput(), new ConsoleOutput());
    }

    /**
     * Dispatch a job to its appropriate handler.
     *
     * @param  string|object  $job
     * @param  mixed  ...$arguments
     * @return PendingDispatch
     */
    final protected function job(string|object $job, ...$arguments): PendingDispatch
    {
        if (is_callable($job)) {
            throw new \Exception('The job must be a string or an object.');
        }

        return dispatch(is_string($job) ? new $job(...$arguments) : $job);
    }

    /**
     * Execute the command.
     *
     * @param string $command
     * @param array $parameters
     * @return int
     */
    final protected function command(string $command, array $parameters = []): int
    {
        return Artisan::call($command, $parameters, $this->output);
    }
}
