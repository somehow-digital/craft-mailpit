<?php

namespace SomehowDigital\Craft\Mailpit;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Utilities;
use SomehowDigital\Craft\Mailpit\models\Settings;
use SomehowDigital\Craft\Mailpit\services\MailpitService;
use SomehowDigital\Craft\Mailpit\utilities\MailpitUtility;
use yii\base\Event;

/**
 * @property Settings $settings
 */
class Mailpit extends Plugin
{
	public bool $hasCpSettings = true;
	public bool $hasCpSection = false;

	public function init(): void
	{
		parent::init();

		Craft::setAlias('@mailpit', $this->getBasePath());

		$settings = $this->getSettings();

		if (Craft::$app->getRequest()->getIsCpRequest()) {
			$this->setComponents([
				'mailpit' => [
					'class' => MailpitService::class,
					'host' => $settings->getHost(),
				],
			]);

			if ($settings->getType() === 'page') {
				$this->hasCpSection = true;
				$this->registerCpRoutes();
			} else if ($this->getSettings()->getType() === 'utility') {
				$this->registerUtility();
			}
		}
	}

	public function getCpNavItem(): ?array
	{
		return array_merge(parent::getCpNavItem(), [
			'url' => 'mailpit',
			'badgeCount' => $this->mailpit->getUnreadCount(),
		]);
	}

	protected function createSettingsModel(): Settings
	{
		return new Settings();
	}

	protected function settingsHtml(): ?string
	{
		return Craft::$app->getView()->renderTemplate(
			'mailpit/settings',
			['settings' => $this->getSettings()]
		);
	}

	protected function registerCpRoutes(): void
	{
		Event::on(
			UrlManager::class,
			UrlManager::EVENT_REGISTER_CP_URL_RULES,
			function (RegisterUrlRulesEvent $event): void {
				$event->rules['mailpit'] = 'mailpit/mailpit/index';
			},
		);
	}

	protected function registerUtility(): void
	{
		Event::on(
			Utilities::class,
			Utilities::EVENT_REGISTER_UTILITIES,
			function (RegisterComponentTypesEvent $event): void {
				$event->types[] = MailpitUtility::class;
			}
		);
	}
}
