<?php

use Illuminate\Support\Facades\Route;
use Mateffy\HtmlReports\Controllers\GenerateIssue;
use Mateffy\HtmlReports\Controllers\RunAgent;

Route::post('/pest-api/generate-issue', GenerateIssue::class)
	->name('pest-reports.generate-issue');

Route::post('/pest-api/run-agent', RunAgent::class)
	->name('pest-reports.run-agent');
