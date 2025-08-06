<?php

use Mateffy\HtmlReports\DTOs\TestCountsDTO;
use Mateffy\HtmlReports\DTOs\TestErrorDTO;
use Mateffy\HtmlReports\DTOs\TestMethodDTO;
use Mateffy\HtmlReports\DTOs\TestSuiteDTO;

describe('TestCountsDTO', function () {
	it('can handle missing data', function () {
		$dto = TestCountsDTO::fromArray([]);
		expect($dto->tests)->toBe(0)
			->and($dto->failed)->toBe(0)
			->and($dto->assertions)->toBe(0)
			->and($dto->errors)->toBe(0)
			->and($dto->warnings)->toBe(0)
			->and($dto->deprecations)->toBe(0)
			->and($dto->notices)->toBe(0)
			->and($dto->success)->toBe(0)
			->and($dto->incomplete)->toBe(0)
			->and($dto->risky)->toBe(0)
			->and($dto->skipped)->toBe(0);
	});
});

describe('TestErrorDTO', function () {
	it('can handle missing data', function () {
		$dto = TestErrorDTO::fromArray([]);
		expect($dto->message)->toBe('')
			->and($dto->exceptionClass)->toBe('')
			->and($dto->line)->toBe(0);
	});
});

describe('TestMethodDTO', function () {
	it('can handle minimal data', function () {
		$dto = TestMethodDTO::fromArray('test name', []);
		expect($dto->name)->toBe('test name')
			->and($dto->description)->toBe('')
			->and($dto->test)->toBe('')
			->and($dto->issues)->toBeNull()
			->and($dto->todo)->toBeNull()
			->and($dto->assignees)->toBeNull()
			->and($dto->prs)->toBeNull()
			->and($dto->notes)->toBeNull()
			->and($dto->skipped)->toBeFalse()
			->and($dto->error)->toBeNull()
			->and($dto->failure)->toBeNull()
			->and($dto->filePath)->toBeNull()
			->and($dto->relativeFilePath)->toBeNull();
	});

	it('can determine success status', function () {
		$dto = TestMethodDTO::fromArray('test name', []);
		expect($dto->getStatus())->toBe('success');
	});

	it('can determine error status', function () {
		$error = TestErrorDTO::fromArray(['message' => 'error msg']);
		$dto = new TestMethodDTO(name: 'test', description: 'desc', test: 'test', error: $error);
		expect($dto->getStatus())->toBe('error');
	});

	it('can determine failure status', function () {
		$failure = TestErrorDTO::fromArray(['message' => 'failure msg']);
		$dto = new TestMethodDTO(name: 'test', description: 'desc', test: 'test', failure: $failure);
		expect($dto->getStatus())->toBe('failure');
	});

	it('can determine skipped status', function () {
		$dto = new TestMethodDTO(name: 'test', description: 'desc', test: 'test', skipped: true);
		expect($dto->getStatus())->toBe('skipped');
	});

	it('can determine todo status', function () {
		$dto = new TestMethodDTO(name: 'test', description: 'desc', test: 'test', todo: true);
		expect($dto->getStatus())->toBe('todo');
	});
});

describe('TestSuiteDTO', function () {
	it('can handle missing tests', function () {
		$dto = TestSuiteDTO::fromArray('suite name', []);
		expect($dto->description)->toBe('suite name')
			->and($dto->tests)->toBe([]);
	});
});
