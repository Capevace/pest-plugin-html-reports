<?php

declare(strict_types=1);

namespace Mateffy\HtmlReports\DTOs;

class TestCountsDTO
{
    public function __construct(
        public readonly int $tests,
        public readonly int $failed,
        public readonly int $assertions,
        public readonly int $errors,
        public readonly int $warnings,
        public readonly int $deprecations,
        public readonly int $notices,
        public readonly int $success,
        public readonly int $incomplete,
        public readonly int $risky,
        public readonly int $skipped
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            tests: $data['tests'] ?? 0,
            failed: $data['failed'] ?? 0,
            assertions: $data['assertions'] ?? 0,
            errors: $data['errors'] ?? 0,
            warnings: $data['warnings'] ?? 0,
            deprecations: $data['deprecations'] ?? 0,
            notices: $data['notices'] ?? 0,
            success: $data['success'] ?? 0,
            incomplete: $data['incomplete'] ?? 0,
            risky: $data['risky'] ?? 0,
            skipped: $data['skipped'] ?? 0
        );
    }

    public function toArray(): array
    {
        return [
            'tests' => $this->tests,
            'failed' => $this->failed,
            'assertions' => $this->assertions,
            'errors' => $this->errors,
            'warnings' => $this->warnings,
            'deprecations' => $this->deprecations,
            'notices' => $this->notices,
            'success' => $this->success,
            'incomplete' => $this->incomplete,
            'risky' => $this->risky,
            'skipped' => $this->skipped,
        ];
    }
}
