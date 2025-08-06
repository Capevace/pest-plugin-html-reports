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
		$testResult = TestResult::result();
		$data = $this->generator->generate($testResult);

		// TODO: Make this configurable
		$outputFile = 'report-{{date}}-{{id}}.json';
		$outputFile = str_replace('{{date}}', now()->format('Y_m_d_H_i_s'), $outputFile);
		$outputFile = str_replace('{{id}}', uniqid(), $outputFile);

		file_put_contents($outputFile, json_encode($data->toArray(), JSON_PRETTY_PRINT));

		return $exitCode;
	}
}
