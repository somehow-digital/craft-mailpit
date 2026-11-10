<?php

declare(strict_types=1);

namespace SomehowDigital\Craft\Mailpit\services;

use Craft;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use yii\base\Component;

/**
 * @internal
 */
final class MailpitService extends Component
{
	public string $host = '';

	public function getUnreadCount(): int
	{
		if ($this->host === '') {
			return 0;
		}

		try {
			// Projects can replace the HTTP client in the container; otherwise Guzzle's default is used.
			$container = Craft::$container;
			$client = $container->has(ClientInterface::class) ? $container->get(ClientInterface::class) : new Client();
			$response = $client->request('GET', rtrim($this->host, '/') . '/api/v1/info');
			$data = json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR);
		} catch (Exception) {
			return 0;
		}

		$unread = is_array($data) ? ($data['Unread'] ?? 0) : 0;

		return is_numeric($unread) ? (int) $unread : 0;
	}
}
