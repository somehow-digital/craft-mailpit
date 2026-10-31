<?php

namespace SomehowDigital\Craft\Mailpit\services;

use GuzzleHttp\Client;
use yii\base\Component;

class MailpitService extends Component
{
	public string $host = '';

	public function getUnreadCount(): int
	{
		if (!$this->host) {
			return 0;
		}

		$client = new Client();

		try {
			$response = $client->get(rtrim($this->host, '/') . '/api/v1/info');
			$data = json_decode($response->getBody(), true);

			return (int)($data['Unread'] ?? 0);
		} catch (\Exception $e) {
			return 0;
		}
	}
}
