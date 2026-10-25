<?php

namespace Pharaonic\Laravel\Executor\Classes;

use Illuminate\Console\Concerns\InteractsWithIO;
use Illuminate\Console\OutputStyle;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Support\Facades\Artisan;
use InvalidArgumentException;
use Pharaonic\Laravel\Executor\Enums\ExecutorType;
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
    public $type = ExecutorType::ALWAYS;

    /**
     * The tags of the executor.
     *
     * @var array<array-key, mixed>
     */
    public $tags = [];

    /**
     * The servers of the executor.
     *
     * @var array<array-key, mixed>
     */
    public $servers = [];

    /**
     * Run the executor.
     */
    abstract public function up(): void;

    /**
     * Reverse the executor.
     */
    abstract public function down(): void;

    /**
     * Create a new Executor instance.
     *
     * @return void
     */
    public function __construct()
    {
        self::validateType($this->type);
        self::validateList($this->tags, 'The tags of the executor must be an array.');
        self::validateList($this->servers, 'The servers of the executor must be an array of ips.');

        $this->output = new OutputStyle(new ArgvInput, new ConsoleOutput);
    }

    /**
     * Dispatch a job to its appropriate handler.
     *
     * @throws InvalidArgumentException
     */
    final protected function job(string|object $job, mixed ...$arguments): PendingDispatch
    {
        if (is_callable($job)) {
            throw new InvalidArgumentException('The job must be a string or an object.');
        }

        return dispatch(is_string($job) ? new $job(...$arguments) : $job);
    }

    /**
     * Execute the command.
     *
     * @param  array<string, mixed>  $parameters
     */
    final protected function command(string $command, array $parameters = []): int
    {
        return Artisan::call($command, $parameters, $this->output);
    }

    /**
     * Executors declare their properties freely, so check them at runtime.
     *
     * @throws InvalidArgumentException
     */
    private static function validateType(mixed $type): void
    {
        if (! $type instanceof ExecutorType) {
            throw new InvalidArgumentException('The type of the executor must be an instance of ExecutorType.');
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private static function validateList(mixed $items, string $message): void
    {
        if (! is_array($items)) {
            throw new InvalidArgumentException($message);
        }
    }
}
