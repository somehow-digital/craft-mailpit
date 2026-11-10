<?php

declare(strict_types=1);

use SomehowDigital\Craft\Mailpit\Tests\TestCase;

const TEST_HOST = 'https://mailpit.example.test';

pest()->extend(TestCase::class)->in('Unit', 'Feature');

/**
 * @template T of object
 * @param class-string<T> $class
 * @return T
 */
function assertInstance(mixed $value, string $class): object
{
	expect($value)->toBeInstanceOf($class);
	assert($value instanceof $class);

	return $value;
}
