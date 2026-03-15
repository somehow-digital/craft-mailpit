<?php

namespace SomehowDigital\Craft\Mailpit\utilities;

use Craft;
use craft\base\Utility;
use SomehowDigital\Craft\Mailpit\Mailpit;

class MailpitUtility extends Utility
{
	public static function displayName(): string
	{
		return Craft::t('mailpit', 'Mailpit');
	}

	public static function id(): string
	{
		return 'mailpit';
	}

	public static function icon(): ?string
	{
		return Craft::getAlias('@mailpit/icon-mask.svg');
	}

	public static function badgeCount(): int
	{
		return Mailpit::getInstance()->mailpit->getUnreadCount();
	}

	public static function contentHtml(): string
	{
		$plugin = Mailpit::getInstance();
		$settings = $plugin->getSettings();

		return Craft::$app->getView()->renderTemplate('mailpit/utility', [
			'settings' => $settings,
		]);
	}

	public static function url(): string
	{
		return 'mailpit';
	}
}
