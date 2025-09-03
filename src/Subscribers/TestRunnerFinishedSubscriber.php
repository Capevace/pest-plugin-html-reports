<?php

declare(strict_types=1);

namespace Mateffy\HtmlReports\Subscribers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Mateffy\HtmlReports\Services\ReportGenerator;
use PHPUnit\Event\TestRunner\Finished;
use PHPUnit\Event\TestRunner\FinishedSubscriber;
use PHPUnit\TestRunner\TestResult\Facade;

final class TestRunnerFinishedSubscriber implements FinishedSubscriber
{
	public function __construct(private readonly ReportGenerator $reportGenerator) {}

	public function notify(Finished $event): void
	{
		file_put_contents('/Users/mat/Downloads/pest-event.json', json_encode($event, JSON_PRETTY_PRINT));
		$testResult = Facade::result();
		$resultJsonData = $this->reportGenerator->generate($testResult);
		$outputFile = getenv('PEST_REPORTS_OUTPUT') ?: 'output.json';
		file_put_contents($outputFile, json_encode($resultJsonData, JSON_PRETTY_PRINT));

		// Call PestReportsCommand to generate the HTML report
		Artisan::call('test-report:generate');
	}
}
