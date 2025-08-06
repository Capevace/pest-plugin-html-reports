<?php

use Mateffy\HtmlReports\Services\PhpStormLinkService;

it('can generate a deep link for phpstorm', function () {
    $service = new PhpStormLinkService('/path/to/project');

    $link = $service->generateDeepLink('/path/to/project/file.php', 123);

    expect($link)->toBe('phpstorm://open?file=file.php&line=123');
});

it('can generate a deep link for vscode', function () {
    $service = new PhpStormLinkService('/path/to/project');

    $link = $service->generateDeepLink('/path/to/project/file.php', 123, 'vscode');

    expect($link)->toBe('vscode://file/file.php:123');
});
