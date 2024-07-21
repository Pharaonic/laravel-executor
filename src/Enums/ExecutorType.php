<?php

namespace Pharaonic\Laravel\Executor\Enums;

enum ExecutorType: int
{
    case Always = 1;
    case Once = 2;
}
