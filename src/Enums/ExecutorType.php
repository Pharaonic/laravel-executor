<?php

namespace Pharaonic\Laravel\Executor\Enums;

use Pharaonic\SmartEnum\SmartEnum;

enum ExecutorType: int
{
    use SmartEnum;

    case ALWAYS = 1;
    case ONCE = 2;

    /**
     * Check if the executor will be executed always.
     */
    public function isAlways(): bool
    {
        return $this === self::ALWAYS;
    }

    /**
     * Check if the executor will be executed once.
     */
    public function isOnce(): bool
    {
        return $this === self::ONCE;
    }

    /**
     * Human-readable label of the type.
     */
    public function label(): string
    {
        return match ($this) {
            self::ALWAYS => 'Always',
            self::ONCE => 'Once',
        };
    }
}
