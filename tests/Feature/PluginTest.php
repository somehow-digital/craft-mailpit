<?php

use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\services\Utilities;
use craft\web\UrlManager;
use SomehowDigital\Craft\Mailpit\Mailpit;
use SomehowDigital\Craft\Mailpit\models\Settings;
use SomehowDigital\Craft\Mailpit\services\MailpitService;
use SomehowDigital\Craft\Mailpit\utilities\MailpitUtility;
use yii\base\Event;

/**
 * The URL rules registered for the control panel.
 *
 * @return array<string,string>
 */
function cpUrlRules(): array
{
	$event = new RegisterUrlRulesEvent();
	Event::trigger(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, $event);

	return $event->rules;
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

it('registers the alias', function () {
	expect(Craft::getAlias('@mailpit'))->toBe(Mailpit::getInstance()->getBasePath());
});

it('has settings', function () {
	expect(Mailpit::getInstance()->hasCpSettings)->toBeTrue();
	expect(Mailpit::getInstance()->getSettings())->toBeInstanceOf(Settings::class);
});

it('passes the host to the service', function () {
	$this->createApp(['host' => 'https://mailpit.example.test']);

	expect(Mailpit::getInstance()->mailpit)->toBeInstanceOf(MailpitService::class);
	expect(Mailpit::getInstance()->mailpit->host)->toBe('https://mailpit.example.test');
});

it('leaves out the service outside of the control panel', function () {
	$this->createApp(isCpRequest: false);

	expect(Mailpit::getInstance()->has('mailpit'))->toBeFalse();
	expect(Mailpit::getInstance()->hasCpSection)->toBeFalse();
	expect(cpUrlRules())->not->toHaveKey('mailpit');
	expect(utilityTypes())->not->toContain(MailpitUtility::class);
});

describe('page type', function () {
	it('adds a section to the control panel', function () {
		expect(Mailpit::getInstance()->hasCpSection)->toBeTrue();
		expect(cpUrlRules())->toMatchArray(['mailpit' => 'mailpit/mailpit/index']);
	});

	it('adds a navigation item linking to the page', function () {
		expect(Mailpit::getInstance()->getCpNavItem())->toMatchArray([
			'url' => 'mailpit',
			'badgeCount' => 0,
		]);
	});

	it('leaves out the utility', function () {
		expect(utilityTypes())->not->toContain(MailpitUtility::class);
	});
});

describe('utility type', function () {
	beforeEach(function () {
		$this->createApp(['type' => 'utility']);
	});

	it('registers the utility', function () {
		expect(utilityTypes())->toContain(MailpitUtility::class);
	});

	it('leaves out the section', function () {
		expect(Mailpit::getInstance()->hasCpSection)->toBeFalse();
		expect(cpUrlRules())->not->toHaveKey('mailpit');
	});

	it('has no badge without a host', function () {
		expect(MailpitUtility::badgeCount())->toBe(0);
	});
});
