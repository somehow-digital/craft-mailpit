<?php

namespace SomehowDigital\Craft\Mailpit\controllers;

use craft\web\Controller;
use SomehowDigital\Craft\Mailpit\Mailpit;
use yii\web\Response;

class MailpitController extends Controller
{
	public function actionIndex(): Response
	{
		$this->requirePermission('accessCp');

		$plugin = Mailpit::getInstance();
		$settings = $plugin->getSettings();

		return $this->renderTemplate('mailpit/page', [
			'settings' => $settings,
		]);
	}
}
