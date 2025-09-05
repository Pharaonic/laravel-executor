<?php

namespace Pharaonic\Laravel\Executor\Classes;

use Illuminate\Console\Concerns\InteractsWithIO;
use Illuminate\Console\OutputStyle;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Support\Facades\Artisan;
use Pharaonic\Laravel\Executor\Enums\ExecutorType;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @property ExecutorType $type
 * @property array $tags
 * @property array $servers
 * @method void up()
 * @method void down()
 * @method PendingDispatch job(string|object $job, ...$arguments)
 * @method int command(string $command, array $parameters = [])
 */
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
     * The tags of the executor.
     *
     * @var array
     */
    public $tags = [];

    /**
     * The servers of the executor.
     *
     * @var array
     */
    public $servers = [];

    /**
     * Run the executor.
     *
     * @return void
     */
    abstract public function up(): void;

    /**
     * Reverse the executor.
     *
     * @return void
     */
    abstract public function down(): void;

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

        if (! is_array($this->tags)) {
            throw new \Exception('The tags of the executor must be an array.');
        }

        if (! is_array($this->servers)) {
            throw new \Exception('The servers of the executor must be an array of ips.');
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
