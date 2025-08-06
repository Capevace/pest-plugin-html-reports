<?php

declare(strict_types=1);

namespace Mateffy\HtmlReports\DTOs;

class TestResultDTO
{
	public function __construct(
		public readonly TestCountsDTO $counts,
		public readonly array $failed,
		public readonly array $testSuites
	) {}

	public static function fromArray(array $data): self
	{
		$testSuites = collect($data['testSuites'] ?? [])
			->map(fn($suiteData, $suiteName) => TestSuiteDTO::fromArray($suiteName, $suiteData))
			->toArray();

		return new self(
			counts: TestCountsDTO::fromArray($data['counts'] ?? []),
			failed: $data['failed'] ?? [],
			testSuites: $testSuites
		);
	}

	public function toArray(): array
	{
		return [
			'counts' => $this->counts->toArray(),
			'failed' => $this->failed,
			'testSuites' => collect($this->testSuites)->map(fn($suite) => $suite->toArray())->toArray(),
		];
	}
}
