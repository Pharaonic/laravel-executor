<?php

namespace Pharaonic\Laravel\Executor\Enums;

/**
 * @method bool isAlways()
 * @method bool isOnce()
 */
enum ExecutorType: int
{
    case Always = 1;
    case Once = 2;

    /**
     * Check if the executor will be executed always.
     *
     * @return boolean
     */
    public function isAlways(): bool
    {
        return $this->value === self::Always->value;
    }

    /**
     * Check if the executor will be executed once.
     *
     * @return boolean
     */
    public function isOnce(): bool
    {
        return $this->value === self::Once->value;
    }
}
