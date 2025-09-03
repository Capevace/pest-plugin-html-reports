<?php

namespace Mateffy\HtmlReports\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Filesystem\LocalFilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Mateffy\HtmlReports\Services\StaticHtmlGenerator;
use Spatie\Watcher\Watch;

class PestReportsCommand extends Command
{
	public $signature = 'test-report:generate
        {--template= : Template for the HTML file, defaults to the config value}
		{--input-dir= : Input directory for the JSON file, defaults to storage/framework/testing/reports}
        {--output-dir= : Output directory for the HTML file, defaults to public/reports}
		{--input= : Path to JSON file}
		{--output= : Path to HTML file}
        {--title= : Title for the report}
        {--project-path= : Project path for relative file paths}
        {--editor=phpstorm : Default editor for deep links}
        {--repository= : GitHub repository (e.g., user/repo)}
		{--watch= : Watch for changes in the input directory}';

	public $description = 'Generate a static HTML report from Pest test results';

	public function __construct(
		protected readonly StaticHtmlGenerator $generator,
	) {
		parent::__construct();
	}

	public function handle(): int
	{
		$this->comment('Generating report...');

		$watch = $this->option('watch');

		// Create custom disk and adapter for the storage/framework/testing/reports directory
		$fs = new FilesystemManager(app());

		$input = $this->option('input');
		$output = $this->option('output');
		$template = $this->option('template') ?? config('pest-reports.html.template');
		$inputDir = $this->option('input-dir') ?? config('pest-reports.json.dir');
		$outputDir = $this->option('output-dir') ?? config('pest-reports.html.dir');

		if ($input) {
			$inputDir = dirname($input);
			$inputDisk = $fs->createLocalDriver(['root' => $inputDir]);
			$inputFilename = pathinfo($input, PATHINFO_FILENAME) . '.json';
		} else {
			$inputDisk = $fs->createLocalDriver(['root' => $inputDir]);

			$inputFilename = collect($inputDisk->files())
				->filter(fn($file) => str_contains($file, 'report-') && str_ends_with($file, '.json'))
				->sortBy(fn($file) => $file)
				->last();

			if (!$inputFilename) {
				$this->error('No JSON file found in the input directory');
				return self::FAILURE;
			}
		}

		if ($output) {
			$outputDir = dirname($output);
			$outputDisk = $fs->createLocalDriver(['root' => $outputDir]);
			$template = pathinfo($output, PATHINFO_FILENAME);
		} else {
			$outputDisk = $fs->createLocalDriver(['root' => $outputDir]);
		}


		if ($watch) {
			$this->watch($inputDisk, $outputDisk, $inputFilename, $template);
		} else {
			return $this->perform($inputDisk, $outputDisk, $inputFilename, $template);
		}
	}

	protected function watch(Filesystem $inputDisk, Filesystem $outputDisk, string $inputFilename, string $template): void
	{
		$this->comment('Watching for changes in the input directory...');

		// Watch for changes in the input directory
		Watch::path($inputDisk->path(''))
			->onFileCreated(function (string $newFilePath) use ($inputDisk, $outputDisk, $inputFilename, $template) {
				$this->perform($inputDisk, $outputDisk, $inputFilename, $template);
			})
			->onFileUpdated(function (string $newFilePath) use ($inputDisk, $outputDisk, $inputFilename, $template) {
				$this->perform($inputDisk, $outputDisk, $inputFilename, $template);
			})
			->onFileDeleted(function (string $deletedFilePath) use ($inputDisk, $outputDisk, $inputFilename, $template) {
				$this->perform($inputDisk, $outputDisk, $inputFilename, $template);
			})
			->start();
	}

	protected function perform(Filesystem $inputDisk, Filesystem $outputDisk, string $inputFilename, string $template): int
	{
		$variables = [
			'{{date}}' => now()->format('Y-m-d'),
			'{{id}}' => str()->random(10),
			'{{filename}}' => pathinfo($inputFilename, PATHINFO_FILENAME),
		];

		$outputFilename = str_replace(array_keys($variables), array_values($variables), $template);

		try {
			$jsonData = $inputDisk->get($inputFilename);

			$html = $this->generator->generateHtmlFromJson($jsonData, [
				'title' => $this->option('title') ?: 'Pest Test Results',
				'projectPath' => $this->option('project-path') ?: '',
				'selectedEditor' => $this->option('editor') ?: 'phpstorm',
				'gitHubRepository' => $this->option('repository') ?: '',
			]);

			$outputDisk->put($outputFilename, $html);

			$finalOutputPath = $outputDisk->path($outputFilename);

			$this->info('Static HTML report generated successfully: ' . $finalOutputPath);

			// If the file was stored in the public/ directory, we show a url to the file!
			if (str_starts_with($finalOutputPath, public_path())) {
				$relativePath = str_replace(public_path(''), '', $finalOutputPath);
				$finalOutputUrl = url($relativePath);

				$this->info('View the report at: ' . $finalOutputUrl);
			}

			return self::SUCCESS;
		} catch (\Exception $e) {
			report($e);

			$this->error('Failed to generate report: ' . $e->getMessage());

			return self::FAILURE;
		}
	}
}
