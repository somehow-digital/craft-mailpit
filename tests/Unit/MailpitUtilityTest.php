<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Response;
use SomehowDigital\Craft\Mailpit\utilities\MailpitUtility;

it('describes the utility', function (): void {
	expect(MailpitUtility::displayName())->toBe('Mailpit');
	expect(MailpitUtility::id())->toBe('mailpit');
	expect(MailpitUtility::url())->toBe('mailpit');
	expect(MailpitUtility::icon())->toEndWith('/icon-mask.svg');
});

it('shows the unread messages as the badge', function (): void {
	$this->createApp(['host' => TEST_HOST, 'type' => 'utility']);
	$this->http(new Response(200, [], '{"Unread": 3}'));

	expect(MailpitUtility::badgeCount())->toBe(3);
});

it('embeds the host', function (): void {
	$this->createApp(['host' => TEST_HOST, 'type' => 'utility']);

	expect(MailpitUtility::contentHtml())->toContain(TEST_HOST);
});
