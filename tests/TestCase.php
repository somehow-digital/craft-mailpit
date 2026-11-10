<?php

declare(strict_types=1);

namespace SomehowDigital\Craft\Mailpit\Tests;

use ArrayObject;
use Closure;
use Craft;
use craft\helpers\ArrayHelper;
use craft\services\Config;
use craft\web\Application;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use SomehowDigital\Craft\Mailpit\Plugin;
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

		$previous = set_error_handler(null);
		restore_error_handler();
		set_error_handler(static function (int $level, string $message, string $file = '', int $line = 0) use ($previous): bool {
			// Craft's Twig nodes aren't `yield`-ready; not ours to fix.
			if ($level === E_USER_DEPRECATED && str_contains($message, 'YieldReady') && str_contains($message, 'craft\\web\\twig\\nodes\\')) {
				return true;
			}

			return $previous !== null && (bool) $previous($level, $message, $file, $line);
		}, E_USER_DEPRECATED);

		$this->createApp();
	}

	protected function tearDown(): void
	{
		foreach ($this->variables as $name) {
			unset($_SERVER[$name]);
		}

		restore_error_handler();

		Event::offAll();
		// @phpstan-ignore assign.propertyType (Yii types the app as always set)
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
		$configService->appDefaultsDir = "{$src}/config/defaults";

		$config = ArrayHelper::merge(
			['vendorPath' => CRAFT_VENDOR_PATH, 'env' => 'test', 'components' => ['config' => $configService]],
			require "{$src}/config/app.php",
			require "{$src}/config/app.web.php",
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
		// @phpstan-ignore argument.type, argument.templateType (Craft's stub only knows the `class` key)
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
	protected function createPlugin(array $settings = []): Plugin
	{
		/** @var Plugin $plugin */
		// @phpstan-ignore argument.type (Craft's stub only knows the `class` key)
		$plugin = Craft::createObject([
			'class' => Plugin::class,
			'basePath' => dirname(__DIR__) . '/src',
			'settings' => $settings,
		], ['mailpit', Craft::$app]);

		Plugin::setInstance($plugin);
		Craft::$app->setModule('mailpit', $plugin);

		/** @var Plugins $plugins */
		$plugins = Craft::$app->getPlugins();
		$plugins->install($plugin);

		return $plugin;
	}

	/**
	 * Mocks the responses of the HTTP client with a response or a callback taking the request.
	 *
	 * The callback may throw, e.g. a `ConnectException` to simulate an outage.
	 *
	 * @param ResponseInterface|Closure(RequestInterface):ResponseInterface $responses
	 * @return ArrayObject<int,RequestInterface> The requests that are sent.
	 */
	protected function http(ResponseInterface|Closure $responses): ArrayObject
	{
		/** @var ArrayObject<int,RequestInterface> $requests */
		$requests = new ArrayObject();

		Craft::$container->set(ClientInterface::class, new Client([
			'handler' => static function (RequestInterface $request) use ($responses, $requests): ResponseInterface {
				$requests[] = $request;

				return $responses instanceof ResponseInterface ? $responses : $responses($request);
			},
		]));

		return $requests;
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
