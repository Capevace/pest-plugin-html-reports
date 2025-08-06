<?php

namespace Mateffy\HtmlReports\Console\Commands;

use Illuminate\Console\Command;
use Mateffy\HtmlReports\Services\StaticHtmlGenerator;

class PestReportsCommand extends Command
{
	public $signature = 'pest-reports:generate 
        {--input= : Path to JSON file with test results}
        {--output= : Output path for the HTML file}
        {--title= : Title for the report}
        {--project-path= : Project path for relative file paths}
        {--editor=phpstorm : Default editor for deep links}
        {--repository= : GitHub repository (e.g., user/repo)}';

	public $description = 'Generate a static HTML report from Pest test results';

	public function handle(StaticHtmlGenerator $generator): int
	{
		$this->comment('Generating report...');

		$inputPath = $this->option('input') ?? 'output.json';
		$outputPath = $this->option('output') ?? 'pest-report.html';

		try {
			$jsonData = file_get_contents($inputPath);
			$html = $generator->generateHtmlFromJson($jsonData, [
				'title' => $this->option('title') ?: 'Pest Test Results',
				'projectPath' => $this->option('project-path') ?: '',
				'selectedEditor' => $this->option('editor') ?: 'phpstorm',
				'gitHubRepository' => $this->option('repository') ?: '',
			]);

			file_put_contents($outputPath, $html);

			$this->info('Static HTML report generated successfully: ' . $outputPath);
			return self::SUCCESS;
		} catch (\Exception $e) {
			report($e);

			$this->error('Failed to generate report: ' . $e->getMessage());

			return self::FAILURE;
		}
	}
}
