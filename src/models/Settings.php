<?php

declare(strict_types=1);

namespace SomehowDigital\Craft\Mailpit\models;

use craft\base\Model;
use craft\helpers\App;

/**
 * @internal
 */
final class Settings extends Model
{
	public string $host = '';

	public string $type = 'page';

	/**
	 * @return list<array<mixed>>
	 */
	public function rules(): array
	{
		return [
			[['type'], 'in', 'range' => ['page', 'utility']],
			[['host'], 'string'],
		];
	}

	public function getHost(): string
	{
		return (string) App::parseEnv($this->host);
	}

	public function getType(): string
	{
		return (string) App::parseEnv($this->type);
	}
}
