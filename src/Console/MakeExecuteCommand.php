<?php

namespace Pharaonic\Laravel\Executor\Console;

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
