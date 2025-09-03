<?php

namespace Mateffy\HtmlReports\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearReportsCommand extends Command
{
	protected $signature = 'test-report:clear {--keep-json} {--keep-html}';
	protected $description = 'Clear the reports';

	public function handle(): int
	{
		$this->info('Clearing reports...');

		$dirs = array_filter([
			$this->option('keep-html')
				? config('pest-reports.html.dir')
				: null,
			$this->option('keep-json')
				? config('pest-reports.json.dir')
				: null,
		]);

		foreach ($dirs as $dir) {
			$this->info('Clearing ' . $dir);
			File::deleteDirectory($dir);
			File::ensureDirectoryExists($dir);
		}

		$this->info('Reports cleared');

		return self::SUCCESS;
	}
}
