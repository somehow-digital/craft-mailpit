<?php

use SomehowDigital\Craft\Mailpit\models\Settings;

it('shows a page without a host by default', function () {
	$settings = new Settings();

	expect($settings->getHost())->toBe('');
	expect($settings->getType())->toBe('page');
});

it('accepts the known types', function (string $type) {
	expect((new Settings(['type' => $type]))->validate())->toBeTrue();
})->with(['page', 'utility']);

it('rejects unknown types', function () {
	$settings = new Settings(['type' => 'unknown']);

	expect($settings->validate())->toBeFalse();
	expect($settings->getErrors())->toHaveKey('type');
});

it('resolves environment variables', function () {
	$this->env([
		'MAILPIT_HOST' => 'https://mailpit.example.test',
		'MAILPIT_TYPE' => 'utility',
	]);

	$settings = new Settings(['host' => '$MAILPIT_HOST', 'type' => '$MAILPIT_TYPE']);

	expect($settings->getHost())->toBe('https://mailpit.example.test');
	expect($settings->getType())->toBe('utility');
});
