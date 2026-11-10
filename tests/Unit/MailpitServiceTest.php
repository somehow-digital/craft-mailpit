<?php

declare(strict_types=1);

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use SomehowDigital\Craft\Mailpit\services\MailpitService;

function mailpitService(string $host = 'https://mailpit.example.test/'): MailpitService
{
	// @phpstan-ignore argument.type (Craft's stub only knows the `class` key)
	$service = Craft::createObject(['class' => MailpitService::class, 'host' => $host]);
	expect($service)->toBeInstanceOf(MailpitService::class);

	return $service;
}

it('has no unread messages without a host', function (): void {
	expect(mailpitService('')->getUnreadCount())->toBe(0);
});

it('does not ask Mailpit without a host', function (): void {
	$requests = $this->http(new Response(200, [], '{"Unread": 7}'));

	expect(mailpitService('')->getUnreadCount())->toBe(0);
	expect($requests)->toHaveCount(0);
});

it('counts the unread messages as a number', function (): void {
	$this->http(new Response(200, [], '{"Unread": "5"}'));

	expect(mailpitService()->getUnreadCount())->toBe(5);
});

it('counts the unread messages', function (): void {
	$requests = $this->http(new Response(200, [], '{"Unread": 7}'));

	expect(mailpitService()->getUnreadCount())->toBe(7);
	expect((string) $requests[0]?->getUri())->toBe('https://mailpit.example.test/api/v1/info');
});

it('has no unread messages when the host is unreachable', function (): void {
	$this->http(static fn(RequestInterface $request): never => throw new ConnectException('Connection refused', $request));

	expect(mailpitService()->getUnreadCount())->toBe(0);
});

it('has no unread messages for an unusable response', function (string $body): void {
	$this->http(new Response(200, [], $body));

	expect(mailpitService()->getUnreadCount())->toBe(0);
})->with([
	'invalid JSON' => 'unavailable',
	'a scalar' => '12',
	'no count' => '{}',
	'a count that is no number' => '{"Unread": "many"}',
]);
