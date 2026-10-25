<?php

declare(strict_types=1);

use Pharaonic\Laravel\Executor\Enums\ExecutorType;

it('keeps the stored values', function () {
    expect(ExecutorType::ALWAYS->value)->toBe(1)
        ->and(ExecutorType::ONCE->value)->toBe(2)
        ->and(ExecutorType::from(1))->toBe(ExecutorType::ALWAYS)
        ->and(ExecutorType::tryFrom(3))->toBeNull();
});

it('tells how often the executor runs', function () {
    expect(ExecutorType::ALWAYS->isAlways())->toBeTrue()
        ->and(ExecutorType::ALWAYS->isOnce())->toBeFalse()
        ->and(ExecutorType::ONCE->isOnce())->toBeTrue()
        ->and(ExecutorType::ONCE->isAlways())->toBeFalse();
});

it('labels the types', function () {
    expect(ExecutorType::ALWAYS->label())->toBe('Always')
        ->and(ExecutorType::ONCE->label())->toBe('Once')
        ->and(ExecutorType::options())->toBe([1 => 'Always', 2 => 'Once']);
});

it('has the smart enum helpers', function () {
    expect(ExecutorType::names())->toBe(['ALWAYS', 'ONCE'])
        ->and(ExecutorType::fromName('ONCE'))->toBe(ExecutorType::ONCE)
        ->and(ExecutorType::ONCE->is(2))->toBeTrue()
        ->and(ExecutorType::ALWAYS->in([ExecutorType::ONCE]))->toBeFalse();
});

it('has no PascalCase case names', function () {
    expect(defined(ExecutorType::class.'::Always'))->toBeFalse()
        ->and(defined(ExecutorType::class.'::Once'))->toBeFalse()
        ->and(ExecutorType::tryFromName('Always'))->toBeNull();
});
