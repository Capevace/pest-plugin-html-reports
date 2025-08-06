<?php

use Mateffy\HtmlReports\Services\PhpStormLinkService;

it('handles empty project path', function () {
	$service = new PhpStormLinkService('');
	$link = $service->generateDeepLink('file.php', 123);
	expect($link)->toBe('phpstorm://open?file=file.php&line=123');
});

it('can set project path after instantiation', function () {
	$service = new PhpStormLinkService();
	$service->setProjectPath('/new/path');
	$link = $service->generateDeepLink('/new/path/file.php', 123);
	expect($link)->toBe('phpstorm://open?file=file.php&line=123');
});

it('uses default editor if unknown one is provided', function () {
	$service = new PhpStormLinkService('/path/to/project');
	$link = $service->generateDeepLink('/path/to/project/file.php', 123, 'unknown-editor');
	expect($link)->toBe('phpstorm://open?file=file.php&line=123');
});

it('can change selected editor', function () {
	$service = new PhpStormLinkService('/path/to/project');
	$service->setSelectedEditor('vscode');
	$link = $service->generateDeepLink('/path/to/project/file.php', 123);
	expect($link)->toBe('vscode://file/file.php:123');
});
