<?php

use SomehowDigital\Craft\Mailpit\services\MailpitService;

it('has no unread messages without a host', function () {
	expect((new MailpitService())->getUnreadCount())->toBe(0);
});

it('has no unread messages when the host is unreachable', function () {
	// Port 1 is reserved and has nothing listening on it, so the connection is refused right away.
	$service = new MailpitService(['host' => 'http://127.0.0.1:1']);

	expect($service->getUnreadCount())->toBe(0);
});
