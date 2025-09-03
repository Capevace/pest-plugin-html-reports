<?php

namespace Mateffy\HtmlReports;

use Mateffy\HtmlReports\Console\Commands\ClearReportsCommand;
use Mateffy\HtmlReports\Console\Commands\PestReportsCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class HtmlReportsServiceProvider extends PackageServiceProvider
{
	public function configurePackage(Package $package): void
	{
		$package
			->name('pest-plugin-html-reports')
			->hasViews('pest-reports')
			->hasConfigFile('pest-reports')
			->hasRoute('pest-reports')
			->hasCommand(PestReportsCommand::class)
			->hasCommand(ClearReportsCommand::class);
	}
}
