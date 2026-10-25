<?php

namespace Pharaonic\Laravel\Executor\Tests\Fixtures;

/**
 * Records the executor methods that ran during a test.
 */
class Recorder
{
    /**
     * @var array<int, string>
     */
    public static array $calls = [];
}
