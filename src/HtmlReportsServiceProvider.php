<?php

namespace Mateffy\HtmlReports;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Mateffy\HtmlReports\Console\Commands\PestReportsCommand;

class HtmlReportsServiceProvider extends PackageServiceProvider
{
	public function configurePackage(Package $package): void
	{
		$package
			->name('pest-reports')
			->hasConfigFile()
			->hasViews()
			->hasCommand(PestReportsCommand::class);
	}
}
