---
title: Settings
description: Point the plugin at your Mailpit instance.
---

Go to **Settings** → **Mailpit** in the Control Panel, or create a `config/mailpit.php` file:

```php
<?php

use craft\helpers\App;

return [
	'host' => App::env('MAILPIT_HOST'),
	'type' => App::env('MAILPIT_TYPE'),
];
```

| Setting              | Config key | Description                                                                                                                  |
| -------------------- | ---------- | ---------------------------------------------------------------------------------------------------------------------------- |
| **Host Address**     | `host`     | The URL of the Mailpit web interface, e.g. `https://mailpit.test`.                                                           |
| **Display Location** | `type`     | `page` shows Mailpit in the [navigation](/features/navigation/) (default), `utility` in the [utilities](/features/utility/). |

:::tip
Both settings support [environment variables](https://craftcms.com/docs/5.x/configure.html#env).
:::

:::note
The plugin only shows Mailpit, it doesn't change how Craft sends emails. Set that up in **Settings** → **Email**, e.g. with the SMTP transport.
:::

:::caution
The Control Panel embeds Mailpit, so the host must be reachable from your browser. An `http://` host inside an `https://` Control Panel is blocked as mixed content.
:::
