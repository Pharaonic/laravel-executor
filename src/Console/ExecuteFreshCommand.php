<?php

namespace Pharaonic\Laravel\Executor\Console;

use Illuminate\Console\Command;
use Pharaonic\Laravel\Executor\Models\Executor;

class ExecuteFreshCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'execute:fresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop all executors and re-create them then execute them';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Executor::truncate();

        $this->call('execute');

        return 0;
    }
}
