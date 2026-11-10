<?php

declare(strict_types=1);

use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\services\Utilities;
use craft\web\UrlManager;
use SomehowDigital\Craft\Mailpit\models\Settings;
use SomehowDigital\Craft\Mailpit\Plugin;
use SomehowDigital\Craft\Mailpit\services\MailpitService;
use SomehowDigital\Craft\Mailpit\utilities\MailpitUtility;
use yii\base\Event;
use yii\base\InvalidConfigException;

/**
 * The URL rules registered for the control panel.
 *
 * @return array<string,string>
 */
function cpUrlRules(): array
{
	$event = new RegisterUrlRulesEvent();
	Event::trigger(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, $event);

	/** @var array<string,string> $rules */
	$rules = $event->rules;

	return $rules;
}

/**
 * The utilities registered for the control panel.
 *
 * @return string[]
 */
function utilityTypes(): array
{
	$event = new RegisterComponentTypesEvent();
	Event::trigger(Utilities::class, Utilities::EVENT_REGISTER_UTILITIES, $event);

	return $event->types;
}

it('registers the alias', function (): void {
	expect(Craft::getAlias('@mailpit'))->toBe(Plugin::instance()->getBasePath());
});

it('has settings', function (): void {
	expect(Plugin::instance()->hasCpSettings)->toBeTrue();
	expect(Plugin::instance()->getSettings())->toBeInstanceOf(Settings::class);
});

it('passes the host to the service', function (): void {
	$this->createApp(['host' => 'https://mailpit.example.test']);

	expect(Plugin::instance()->mailpit)->toBeInstanceOf(MailpitService::class);
	expect(Plugin::instance()->mailpit->host)->toBe('https://mailpit.example.test');
});

it('leaves out the service outside of the control panel', function (): void {
	$this->createApp(isCpRequest: false);

	expect(Plugin::instance()->has('mailpit'))->toBeFalse();
	expect(Plugin::instance()->hasCpSection)->toBeFalse();
	expect(cpUrlRules())->not->toHaveKey('mailpit');
	expect(utilityTypes())->not->toContain(MailpitUtility::class);
});

describe('page type', function (): void {
	it('adds a section to the control panel', function (): void {
		expect(Plugin::instance()->hasCpSection)->toBeTrue();
		expect(cpUrlRules())->toMatchArray(['mailpit' => 'mailpit/mailpit/index']);
	});

	it('adds a navigation item linking to the page', function (): void {
		expect(Plugin::instance()->getCpNavItem())->toMatchArray([
			'url' => 'mailpit',
			'badgeCount' => 0,
		]);
	});

	it('leaves out the utility', function (): void {
		expect(utilityTypes())->not->toContain(MailpitUtility::class);
	});
});

describe('utility type', function (): void {
	beforeEach(function (): void {
		$this->createApp(['type' => 'utility']);
	});

	it('registers the utility', function (): void {
		expect(utilityTypes())->toContain(MailpitUtility::class);
	});

	it('leaves out the section', function (): void {
		expect(Plugin::instance()->hasCpSection)->toBeFalse();
		expect(cpUrlRules())->not->toHaveKey('mailpit');
	});

	it('has no badge without a host', function (): void {
		expect(MailpitUtility::badgeCount())->toBe(0);
	});
});

it('throws when the plugin is not loaded', function (): void {
	Plugin::setInstance(null);

	Plugin::instance();
})->throws(InvalidConfigException::class, 'The Mailpit plugin is not loaded.');

it('renders the settings with the current ones', function (): void {
	$this->createApp(['host' => 'https://mailpit.example.test']);

	$html = (new ReflectionMethod(Plugin::class, 'settingsHtml'))->invoke(Plugin::instance());

	expect($html)->toContain('https://mailpit.example.test');
});

it('is initialized as a plugin', function (): void {
	// Set by Craft's plugin for the settings it renders itself.
	expect(Plugin::instance()->hasReadOnlyCpSettings)->toBeTrue();
});
