<?php

declare(strict_types=1);

namespace SomehowDigital\Craft\Mailpit\utilities;

use Craft;
use craft\base\Utility;
use SomehowDigital\Craft\Mailpit\Plugin;

/**
 * @internal
 */
final class MailpitUtility extends Utility
{
	public static function displayName(): string
	{
		return Craft::t('mailpit', 'Mailpit');
	}

	public static function id(): string
	{
		return 'mailpit';
	}

	public static function icon(): string
	{
		return Craft::getAlias('@mailpit/icon-mask.svg');
	}

	public static function badgeCount(): int
	{
		return Plugin::instance()->mailpit->getUnreadCount();
	}

	public static function contentHtml(): string
	{
		return Craft::$app->getView()->renderTemplate('mailpit/utility', [
			'settings' => Plugin::instance()->getSettings(),
		]);
	}

	public static function url(): string
	{
		return 'mailpit';
	}
}
