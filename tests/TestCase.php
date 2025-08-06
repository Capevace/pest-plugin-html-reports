<?php

namespace Mateffy\HtmlReports\Tests;

use Illuminate\Support\Facades\View;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            \Mateffy\HtmlReports\HtmlReportsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        View::addNamespace('pest-reports', __DIR__.'/../resources/views');
    }
}
