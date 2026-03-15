<img src="./src/icon.svg" width="100" height="100" alt="Mailpit icon">

# `Mailpit` for Craft CMS
> Provides a [Mailpit](https://mailpit.axllent.org/) integration for [Craft CMS](https://craftcms.com/).

## Features

- Display the Mailpit web interface in a dedicated Control Panel section or as Utility.
- Unread message count badge in the sidebar navigation or utility icon.
- Simple configuration with support for environment variables.

## Requirements

* Craft CMS 5.8.0 or later.
* PHP 8.2 or later.

## Installation

Install this plugin from the Plugin Store or via Composer.

#### Plugin Store

Go to the “Plugin Store” in your project’s Control Panel, search for
“Mailpit” and click on the “Install” button in its modal window.

#### Composer

```sh
composer require somehow-digital/craft-mailpit
./craft plugin/install mailpit
```

## Configuration

You can configure the plugin in the Control Panel under **Settings** → **Mailpit**, or by creating a `config/mailpit.php` file:

```php
<?php

return [
  'host' => App::env('MAILPIT_HOST'),
  'type' => App::env('MAILPIT_TYPE'), // 'page' or 'utility'
];
```
