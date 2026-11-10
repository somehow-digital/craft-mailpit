<?php

declare(strict_types=1);

use craft\elements\User;
use craft\web\Response;
use craft\web\TemplateResponseBehavior;
use SomehowDigital\Craft\Mailpit\models\Settings;
use yii\web\ForbiddenHttpException;

it('renders the page with the settings', function (): void {
	$this->createApp(['host' => 'https://mailpit.example.test']);
	Craft::$app->getUser()->setIdentity(new User(['admin' => true]));

	$response = assertInstance(Craft::$app->runAction('mailpit/mailpit/index'), Response::class);
	$template = assertInstance($response->getBehavior(TemplateResponseBehavior::NAME), TemplateResponseBehavior::class);
	$settings = assertInstance($template->variables['settings'] ?? null, Settings::class);

	expect($template->template)->toBe('mailpit/page');
	expect($settings->host)->toBe('https://mailpit.example.test');
});

// Action requests from the site aren't checked by Craft, so the action checks the permission itself.
it('requires access to the control panel', function (): void {
	$this->createApp(['host' => 'https://mailpit.example.test'], isCpRequest: false);
	Craft::$app->getConfig()->getGeneral()->isSystemLive = true;
	Craft::$app->getUser()->setIdentity(new class (['admin' => false]) extends User {
		public function can(string $permission): bool
		{
			return false;
		}
	});

	Craft::$app->runAction('mailpit/mailpit/index');
})->throws(ForbiddenHttpException::class);
