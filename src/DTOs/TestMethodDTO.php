<?php

declare(strict_types=1);

namespace Mateffy\HtmlReports\DTOs;

use Illuminate\Support\Str;

class TestMethodDTO
{
	public function __construct(
		public readonly string $id,
		public readonly string $name,
		public readonly string $description,
		public readonly string $test,
		public readonly ?array $issues = null,
		public readonly ?bool $todo = null,
		public readonly ?array $assignees = null,
		public readonly ?array $prs = null,
		public readonly ?array $notes = null,
		public readonly ?array $screenshots = null,
		public readonly bool $skipped = false,
		public readonly ?TestErrorDTO $error = null,
		public readonly ?TestErrorDTO $failure = null,
		public readonly ?string $filePath = null,
		public readonly ?string $relativeFilePath = null
	) {}

	public static function fromArray(string $name, array $data, ?string $basePath = null): self
	{
		$filePath = $data['filePath'] ?? null;
		$relativeFilePath = null;

		if ($filePath && $basePath) {
			$relativeFilePath = Str::after($filePath, $basePath . '/');
		}

		$method = new self(
			id: $data['id'] ?? Str::uuid()->toString(),
			name: $name,
			description: $data['description'] ?? '',
			test: $data['test'] ?? '',
			issues: $data['issues'] ?? null,
			todo: $data['todo'] ?? null,
			assignees: $data['assignees'] ?? null,
			prs: $data['prs'] ?? null,
			notes: $data['notes'] ?? null,
			screenshots: $data['screenshots'] ?? null,
			skipped: $data['skipped'] ?? false,
			error: isset($data['error']) ? TestErrorDTO::fromArray($data['error']) : null,
			failure: isset($data['failure']) ? TestErrorDTO::fromArray($data['failure']) : null,
			filePath: $filePath,
			relativeFilePath: $relativeFilePath
		);

		return $method;
	}

	public function toArray(): array
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'description' => $this->description,
			'test' => $this->test,
			'issues' => $this->issues,
			'todo' => $this->todo,
			'assignees' => $this->assignees,
			'prs' => $this->prs,
			'notes' => $this->notes,
			'screenshots' => $this->screenshots,
			'skipped' => $this->skipped,
			'error' => $this->error?->toArray(),
			'failure' => $this->failure?->toArray(),
			'filePath' => $this->filePath,
			'relativeFilePath' => $this->relativeFilePath,
		];
	}

	public function getStatus(): string
	{
		if ($this->error) {
			return 'error';
		}
		if ($this->failure) {
			return 'failure';
		}
		if ($this->skipped) {
			return 'skipped';
		}
		if ($this->todo) {
			return 'todo';
		}

		return 'success';
	}
}
