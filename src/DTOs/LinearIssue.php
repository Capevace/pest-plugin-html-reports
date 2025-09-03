<?php

declare(strict_types=1);

namespace Mateffy\HtmlReports\DTOs;

class LinearIssue
{
	public function __construct(
		public readonly string $title,
		public readonly string $description,
	) {}

	public static function fromArray(array $data): self
	{
		return new self(
			title: $data['title'],
			description: $data['description'],
		);
	}

	public function toArray(): array
	{
		return [
			'title' => $this->title,
			'description' => $this->description,
		];
	}
}
