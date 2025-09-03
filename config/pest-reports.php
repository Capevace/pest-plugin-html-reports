<?php

return [
	'json' => [
		'dir' => env('TEST_JSON_REPORT_DIR', storage_path('framework/testing/reports')),
		'template' => 'report-{{date}}-{{id}}.json',
	],
	'html' => [
		'dir' => env('TEST_HTML_REPORT_DIR', public_path('reports')),
		'template' => 'test-report.html',
	],
];
