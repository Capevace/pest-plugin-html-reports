<?php

namespace Mateffy\HtmlReports;

use Illuminate\Support\Facades\Artisan;
use Mateffy\HtmlReports\Services\ReportGenerator;
use Pest\Contracts\Plugins\AddsOutput;
use PHPUnit\TestRunner\TestResult\Facade as TestResultFacade;
use PHPUnit\TestRunner\TestResult\TestResult;

class ReportPlugin implements AddsOutput
{
	private ?TestResult $testResult = null;

	public function __construct(private readonly ReportGenerator $generator) {}

	public function setTestResult(TestResult $testResult): void
	{
		$this->testResult = $testResult;
	}

	public function addOutput(int $exitCode): int
	{
		$testResult = $this->testResult ?? TestResultFacade::result();
		$data = $this->generator->generate($testResult);
		$cwd = getcwd();

		$outputDir = getenv('TEST_REPORT_DIR') ?: "{$cwd}/storage/framework/testing/reports";
		if (! is_dir($outputDir)) {
			mkdir($outputDir, 0755, true);
		}

		$outputFile = getenv('TEST_REPORT_FILENAME') ?: 'report-{{date}}-{{id}}.json';
		$outputFile = str_replace('{{date}}', now()->format('Y_m_d_H_i_s'), $outputFile);
		$outputFile = str_replace('{{id}}', uniqid(), $outputFile);
		$outputPath = rtrim($outputDir, '/') . '/' . $outputFile;

		file_put_contents($outputPath, json_encode($data->toArray(), JSON_PRETTY_PRINT));

		// Call PestReportsCommand to generate the HTML report
		$result = shell_exec('php artisan test-report:generate');

		print_r($result);

		return $exitCode;
	}
}
