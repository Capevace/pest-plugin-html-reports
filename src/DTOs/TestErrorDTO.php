<?php

declare(strict_types=1);

namespace Mateffy\HtmlReports\DTOs;

class TestErrorDTO
{
    public function __construct(
        public readonly string $message,
        public readonly string $exceptionClass,
        public readonly int $line
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            message: $data['message'] ?? '',
            exceptionClass: $data['exception_class'] ?? '',
            line: $data['line'] ?? 0
        );
    }

    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'exceptionClass' => $this->exceptionClass,
            'line' => $this->line,
        ];
    }
}
