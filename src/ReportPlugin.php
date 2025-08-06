<?php

namespace Mateffy\HtmlReports;

use Mateffy\HtmlReports\Services\ReportGenerator;
use Pest\Contracts\Plugins\AddsOutput;
use PHPUnit\TestRunner\TestResult\Facade as TestResult;

class ReportPlugin implements AddsOutput
{
	public function __construct(private readonly ReportGenerator $generator) {}

	public function addOutput(int $exitCode): int
	{
		dd('test');

		$testResult = TestResult::result();
		$resultJsonData = $this->generator->generate($testResult);

		$outputFile = config('pest-reports.filename_template');
		$outputFile = str_replace('{{date}}', now()->format('Y_m_d_H_i_s'), $outputFile);
		$outputFile = str_replace('{{id}}', uniqid(), $outputFile);

		file_put_contents($outputFile, json_encode($resultJsonData, JSON_PRETTY_PRINT));

		return $exitCode;
	}
}
