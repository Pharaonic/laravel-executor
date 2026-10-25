<?php

namespace Pharaonic\Laravel\Executor\Console;

/**
 * @deprecated Use the make:executor command (MakeExecutorCommand).
 */
class MakeExecuteCommand extends ExecuteMakeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:execute {name}
                            {--path= : The path of the executor}';
}
