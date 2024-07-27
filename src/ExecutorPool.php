<?php

namespace Pharaonic\Laravel\Executor;

class ExecutorPool
{
    private static $paths = [];

    public static function getPaths()
    {
        return static::$paths;
    }

    public static function addPath($path)
    {
        array_push(static::$paths, $path);
    }
}
