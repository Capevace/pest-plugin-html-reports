<?php

namespace Mateffy\HtmlReports;

use Mateffy\HtmlReports\Console\Commands\PestReportsCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
