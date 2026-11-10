<?php

declare(strict_types=1);

use SomehowDigital\Craft\Mailpit\models\Settings;

it('shows a page without a host by default', function (): void {
	$settings = new Settings();

	expect($settings->getHost())->toBe('');
	expect($settings->getType())->toBe('page');
});

it('accepts the known types', function (string $type): void {
	expect((new Settings(['type' => $type]))->validate())->toBeTrue();
})->with(['page', 'utility']);

it('rejects unknown types', function (): void {
	$settings = new Settings(['type' => 'unknown']);

	expect($settings->validate())->toBeFalse();
	expect($settings->getErrors())->toHaveKey('type');
});

it('takes the host and type from the control panel', function (): void {
	$settings = new Settings();
	$settings->setAttributes(['host' => TEST_HOST, 'type' => 'utility']);

	expect($settings->host)->toBe(TEST_HOST);
	expect($settings->type)->toBe('utility');
});

it('resolves unset environment variables to nothing', function (): void {
	$settings = new Settings(['host' => '$MAILPIT_UNSET_HOST', 'type' => '$MAILPIT_UNSET_TYPE']);

	expect($settings->getHost())->toBe('');
	expect($settings->getType())->toBe('');
});

it('resolves environment variables', function (): void {
	$this->env([
		'MAILPIT_HOST' => TEST_HOST,
		'MAILPIT_TYPE' => 'utility',
	]);

	$settings = new Settings(['host' => '$MAILPIT_HOST', 'type' => '$MAILPIT_TYPE']);

	expect($settings->getHost())->toBe(TEST_HOST);
	expect($settings->getType())->toBe('utility');
});
