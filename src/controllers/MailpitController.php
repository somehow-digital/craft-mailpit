<?php

declare(strict_types=1);

namespace SomehowDigital\Craft\Mailpit\controllers;

use craft\web\Controller;
use SomehowDigital\Craft\Mailpit\Plugin;
use yii\web\Response;

/**
 * @internal
 */
final class MailpitController extends Controller
{
	public function actionIndex(): Response
	{
		$this->requirePermission('accessCp');

		return $this->renderTemplate('mailpit/page', [
			'settings' => Plugin::instance()->getSettings(),
		]);
	}
}
