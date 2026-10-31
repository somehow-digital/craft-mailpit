<?php

namespace SomehowDigital\Craft\Mailpit\Tests;

use Craft;
use craft\helpers\ArrayHelper;
use craft\services\Config;
use craft\web\Application;
use PHPUnit\Framework\TestCase as BaseTestCase;
use SomehowDigital\Craft\Mailpit\Mailpit;
use SomehowDigital\Craft\Mailpit\Tests\Support\Plugins;
use yii\base\Event;
use yii\caching\ArrayCache;
use yii\di\Container;

/**
 * Boots a fresh Craft app with the plugin for each test, without a database.
 */
abstract class TestCase extends BaseTestCase
{
	/** @var string[] names of the environment variables set by the test */
	private array $variables = [];

	protected function setUp(): void
	{
		parent::setUp();

		$this->createApp();
	}

	protected function tearDown(): void
	{
		foreach ($this->variables as $name) {
			unset($_SERVER[$name]);
		}

		Event::offAll();
		Craft::$app = null;
		Craft::$container = new Container();

		parent::tearDown();
	}

	/**
	 * Boots Craft, which the plugin reads its settings and the kind of request from when it initializes.
	 *
	 * @param array<string,mixed> $settings
	 */
	protected function createApp(array $settings = [], bool $isCpRequest = true): Application
	{
		Event::offAll();
		Craft::$container = new Container();

		$src = CRAFT_VENDOR_PATH . '/craftcms/cms/src';

		$configService = new Config();
		$configService->appType = 'web';
		$configService->env = 'test';
		$configService->configDir = Craft::getAlias('@config');
		$configService->appDefaultsDir = "$src/config/defaults";

		$config = ArrayHelper::merge(
			['vendorPath' => CRAFT_VENDOR_PATH, 'env' => 'test', 'components' => ['config' => $configService]],
			require "$src/config/app.php",
			require "$src/config/app.web.php",
			[
				'isInstalled' => false,
				'components' => [
					'log' => ['targets' => []],
					'cache' => ['class' => ArrayCache::class],
					'plugins' => ['class' => Plugins::class],
				],
			],
		);

		/** @var Application $app */
		$app = Craft::createObject($config);

		// Tests run on the command line, which requests take for console requests.
		$app->getRequest()->setIsConsoleRequest(false);
		$app->getRequest()->setIsCpRequest($isCpRequest);

		$this->createPlugin($settings);

		return $app;
	}

	/**
	 * @param array<string,mixed> $settings
	 */
	protected function createPlugin(array $settings = []): Mailpit
	{
		/** @var Mailpit $plugin */
		$plugin = Craft::createObject([
			'class' => Mailpit::class,
			'basePath' => dirname(__DIR__) . '/src',
			'settings' => $settings,
		], ['mailpit', Craft::$app]);

		Mailpit::setInstance($plugin);
		Craft::$app->setModule('mailpit', $plugin);

		/** @var Plugins $plugins */
		$plugins = Craft::$app->getPlugins();
		$plugins->install($plugin);

		return $plugin;
	}

	/**
	 * Sets environment variables, which are removed again after the test.
	 *
	 * @param array<string,string> $variables
	 */
	protected function env(array $variables): void
	{
		foreach ($variables as $name => $value) {
			$_SERVER[$name] = $value;
			$this->variables[] = $name;
		}
	}
}
