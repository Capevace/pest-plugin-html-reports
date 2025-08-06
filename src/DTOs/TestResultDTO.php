<?php

declare(strict_types=1);

namespace Mateffy\HtmlReports\DTOs;

class TestResultDTO
{
    public function __construct(
        public readonly TestCountsDTO $counts,
        public readonly array $testSuites
    ) {}

    public static function fromArray(array $data, ?string $basePath = null): self
    {
        $testSuites = collect($data['testSuites'] ?? [])
            ->map(fn ($suiteData, $suiteName) => TestSuiteDTO::fromArray($suiteName, $suiteData, $basePath))
            ->toArray();

        return new self(
            counts: TestCountsDTO::fromArray($data['counts'] ?? []),
            testSuites: $testSuites
        );
    }

    public function toArray(): array
    {
        return [
            'counts' => $this->counts->toArray(),
            'testSuites' => collect($this->testSuites)->map(fn ($suite) => $suite->toArray())->toArray(),
        ];
    }
}
