<?php

use Mateffy\HtmlReports\Services\PhpStormLinkService;

it('handles empty project path', function () {
	$file = 'file-' . uniqid() . '.php';
	$line = random_int(1, 1000000);

	$service = new PhpStormLinkService('');
	$link = $service->generateDeepLink($file, $line);
	expect($link)->toBe('phpstorm://open?file=' . $file . '&line=' . $line);
});

it('can set project path after instantiation', function () {
	$file = 'file-' . uniqid() . '.php';
	$line = random_int(1, 1000000);

	$service = new PhpStormLinkService;
	$service->setProjectPath('/new/path');
	$link = $service->generateDeepLink('/new/path/' . $file, $line);
	expect($link)->toBe('phpstorm://open?file=' . $file . '&line=' . $line);
});

it('uses default editor if unknown one is provided', function () {
	$file = 'file-' . uniqid() . '.php';
	$line = random_int(1, 1000000);

	$service = new PhpStormLinkService('/path/to/project');
	$link = $service->generateDeepLink('/path/to/project/' . $file, $line, 'unknown-editor');
	expect($link)->toBe('phpstorm://open?file=' . $file . '&line=' . $line);
});

it('can change selected editor', function () {
	$file = 'file-' . uniqid() . '.php';
	$line = random_int(1, 1000000);

	$service = new PhpStormLinkService('/path/to/project');
	$service->setSelectedEditor('vscode');
	$link = $service->generateDeepLink('/path/to/project/' . $file, $line);
	expect($link)->toBe('vscode://file/' . $file . ':' . $line);
});
