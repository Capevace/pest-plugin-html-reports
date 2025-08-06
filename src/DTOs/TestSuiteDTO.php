<?php

declare(strict_types=1);

namespace Mateffy\HtmlReports\DTOs;

class TestSuiteDTO
{
    public function __construct(
        public readonly string $description,
        public readonly array $tests
    ) {}

    public static function fromArray(string $description, array $data, ?string $basePath = null): self
    {
        $tests = collect($data['tests'] ?? [])
            ->map(fn ($testData, $testName) => TestMethodDTO::fromArray($testName, $testData, $basePath))
            ->toArray();

        return new self(
            description: $description,
            tests: $tests
        );
    }

    public function toArray(): array
    {
        return [
            'description' => $this->description,
            'tests' => collect($this->tests)->map(fn ($test) => $test->toArray())->toArray(),
        ];
    }
}
