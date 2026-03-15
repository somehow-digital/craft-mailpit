<?php

namespace SomehowDigital\Craft\Mailpit\models;

use craft\base\Model;
use craft\helpers\App;

class Settings extends Model
{
	public string $host = '';
	public string $type = 'page';

	public function rules(): array
	{
		return [
			[['type'], 'in', 'range' => ['page', 'utility']],
			[['host'], 'string'],
		];
	}

	public function getHost(): string
	{
		return App::parseEnv($this->host);
	}

	public function getType(): string
	{
		return App::parseEnv($this->type);
	}
}
