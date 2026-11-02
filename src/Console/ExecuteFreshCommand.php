<?php

namespace Pharaonic\Laravel\Executor\Console;

use Illuminate\Console\Command;
use Pharaonic\Laravel\Executor\Console\Concerns\RunsWithLock;
use Pharaonic\Laravel\Executor\Models\Executor;

class ExecuteFreshCommand extends Command
{
    use RunsWithLock;

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
        $code = $this->withLock(fn () => Executor::query()->delete());

        return $code === 0 ? $this->call('execute') : $code;
    }
}
