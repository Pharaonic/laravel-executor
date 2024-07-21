<?php

namespace Pharaonic\Laravel\Executor\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ExecuteMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'execute:make {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new executor class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Executor';

    /**
     * Get the stub file for the generator.
     * 
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../../stubs/executor.php.stub';
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::snake(trim(str_replace('\\', '/', Str::replaceFirst($this->rootNamespace(), '', $name))));

        File::ensureDirectoryExists($dir = base_path('executors'));

        return $dir . DIRECTORY_SEPARATOR . $this->getDatePrefix() . '_' . $name . '.php';
    }

    /**
     * Get the date prefix for the migration.
     *
     * @return string
     */
    protected function getDatePrefix()
    {
        return date('Y_m_d_His');
    }
}
