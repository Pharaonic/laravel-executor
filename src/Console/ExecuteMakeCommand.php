<?php

namespace Pharaonic\Laravel\Executor\Console;

/**
 * @deprecated Use the make:executor command (MakeExecutorCommand).
 */
class ExecuteMakeCommand extends MakeExecutorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'execute:make {name}
                            {--path= : The path of the executor}';

    /**
     * Indicates whether the command should be shown in the Artisan command list.
     *
     * @var bool
     */
    protected $hidden = true;

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $this->warn("The {$this->getName()} command is deprecated. Use make:executor instead.");

        return parent::handle();
    }
}
