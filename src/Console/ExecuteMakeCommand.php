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
    protected $signature = 'execute:make {name}
                            {--o|once : Create a new executor class that will be executed once}
                            {--tag= : The tag of the executor}
                            {--path= : The path of the executor}';

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
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $stub = parent::replaceClass($stub, $name);

        $this->setTypeProperty($stub);
        $this->setTagProperty($stub);

        return $stub;
    }

    /**
     * Set the type property for the executor.
     *
     * @param string $stub
     * @return void
     */
    protected function setTypeProperty(string &$stub)
    {
        if (!$this->option('once')) {
            $stub = str_replace(
                [
                    "{{ type }}\n",
                    "{{ type-use }}\n"
                ],
                '',
                $stub
            );

            return;
        }

        $stub = str_replace(
            [
                '{{ type-use }}',
                '{{ type }}',
            ],
            [
                'use Pharaonic\Laravel\Executor\Enums\ExecutorType;',
                file_get_contents(__DIR__ . '/../../stubs/property/once.stub') . PHP_EOL,
            ],
            $stub
        );
    }

    /**
     * Replace the once option for the given stub.
     *
     * @param string $stub
     * @return void
     */
    protected function setTagProperty(string &$stub)
    {
        if (!$this->option('tag')) {
            $stub = str_replace("{{ tag }}\n", '', $stub);
            return;
        }

        $stub = str_replace(
            '{{ tag }}',
            str_replace(
                '{{ tag }}',
                '"' . $this->option('tag') . '"',
                file_get_contents(__DIR__ . '/../../stubs/property/tag.stub')
            ) . PHP_EOL,
            $stub
        );
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::snake(
            trim(
                str_replace(
                    '\\',
                    '/',
                    Str::replaceFirst(
                        $this->rootNamespace(),
                        '',
                        $name
                    )
                )
            )
        );

        File::ensureDirectoryExists($dir = base_path($this->option('path') ?? 'executors'));

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
