<?php

namespace Pharaonic\Laravel\Executor\Console;

use Illuminate\Console\Command;
use Pharaonic\Laravel\Executor\Facades\Executor;

class ExecuteStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'execute:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show the status of the executors.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->table(
            ['Name', 'Type', 'Tags', 'Servers', 'Batch', 'Executed'],
            array_map(function ($executor) {
                return [
                    $executor['name'],
                    $executor['type']->label(),
                    empty($executor['tags']) ? 'None' : $this->implode($executor['tags']),
                    empty($executor['servers']) ? 'All' : $this->implode($executor['servers']),
                    $executor['batch'] ?? '<comment>N/A</comment>',
                    $executor['executed'] ? '<info>Yes</info>' : '<comment>No</comment>',
                ];
            }, array_values(Executor::info()))
        );

        return 0;
    }

    /**
     * @param  array<array-key, mixed>  $values
     */
    private function implode(array $values): string
    {
        return implode(', ', array_map(fn ($value) => is_scalar($value) ? (string) $value : (json_encode($value) ?: ''), $values));
    }
}
