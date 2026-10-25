<?php

declare(strict_types=1);

namespace Pharaonic\Laravel\Executor\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as Orchestra;
use Pharaonic\Laravel\Executor\ExecutorServiceProvider;
use Pharaonic\Laravel\Executor\Tests\Fixtures\Recorder;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        File::deleteDirectory(base_path('executors'));
        File::deleteDirectory(base_path('custom-executors'));
        Recorder::$calls = [];
    }

    protected function getPackageProviders($app)
    {
        return [ExecutorServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('pharaonic.executor.connection', 'testing');
    }

    /**
     * Write an executor file that records its up() and down() calls.
     */
    protected function writeExecutor(string $name, string $properties = '', string $directory = 'executors'): string
    {
        File::ensureDirectoryExists($dir = base_path($directory));

        File::put($path = $dir.DIRECTORY_SEPARATOR.$name.'.php', <<<PHP
<?php

use Pharaonic\Laravel\Executor\Classes\Executor;
use Pharaonic\Laravel\Executor\Enums\ExecutorType;
use Pharaonic\Laravel\Executor\Tests\Fixtures\Recorder;

return new class () extends Executor {
    {$properties}

    public function up(): void
    {
        Recorder::\$calls[] = 'up:{$name}';
    }

    public function down(): void
    {
        Recorder::\$calls[] = 'down:{$name}';
    }
};
PHP);

        return $path;
    }
}
