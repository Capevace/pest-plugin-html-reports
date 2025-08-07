<?php

use Mateffy\HtmlReports\ReportPlugin;
use Mateffy\HtmlReports\Services\ReportGenerator;
use PHPUnit\TestRunner\TestResult\TestResult as PhpUnitResult;
use ReflectionClass;

beforeEach(function () {
    $this->reportGenerator = Mockery::mock(ReportGenerator::class);

    $reflection = new ReflectionClass(PhpUnitResult::class);
    $constructor = $reflection->getConstructor();
    $constructor->setAccessible(true);
    $this->testResult = $reflection->newInstanceWithoutConstructor();
});

it('uses default directory and filename when environment variables are not set', function () {
    $this->reportGenerator
        ->shouldReceive('generate')
        ->once()
        ->andReturn(new \Mateffy\HtmlReports\DTOs\TestResultDTO(
            counts: new \Mateffy\HtmlReports\DTOs\TestCountsDTO(
                tests: 0,
                failed: 0,
                assertions: 0,
                errors: 0,
                warnings: 0,
                deprecations: 0,
                notices: 0,
                success: 0,
                incomplete: 0,
                risky: 0,
                skipped: 0
            ),
            testSuites: []
        ));

    $plugin = new ReportPlugin($this->reportGenerator);
    $plugin->setTestResult($this->testResult);
    $plugin->addOutput(0);

    $files = glob('storage/framework/testing/reports/report-*.json');
    expect($files)->not->toBeEmpty();
    array_map('unlink', glob('storage/framework/testing/reports/*'));
    rmdir('storage/framework/testing/reports');
});

it('uses environment variables for directory and filename', function () {
    putenv('TEST_REPORT_DIR=temp_reports');
    putenv('TEST_REPORT_FILENAME=custom-report.json');

    $this->reportGenerator
        ->shouldReceive('generate')
        ->once()
        ->andReturn(new \Mateffy\HtmlReports\DTOs\TestResultDTO(
            counts: new \Mateffy\HtmlReports\DTOs\TestCountsDTO(
                tests: 0,
                failed: 0,
                assertions: 0,
                errors: 0,
                warnings: 0,
                deprecations: 0,
                notices: 0,
                success: 0,
                incomplete: 0,
                risky: 0,
                skipped: 0
            ),
            testSuites: []
        ));

    $plugin = new ReportPlugin($this->reportGenerator);
    $plugin->setTestResult($this->testResult);
    $plugin->addOutput(0);

    expect(file_exists('temp_reports/custom-report.json'))->toBeTrue();
    unlink('temp_reports/custom-report.json');
    rmdir('temp_reports');

    // Clear the environment variables
    putenv('TEST_REPORT_DIR');
    putenv('TEST_REPORT_FILENAME');
});
