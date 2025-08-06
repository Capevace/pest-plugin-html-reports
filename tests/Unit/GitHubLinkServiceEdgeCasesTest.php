<?php

use Mateffy\HtmlReports\Services\GitHubLinkService;

it('returns null for pr url if repo is empty', function () {
    $service = new GitHubLinkService('');
    $url = $service->generatePullRequestUrl(123);
    expect($url)->toBeNull();
});

it('returns null for issue url if repo is empty', function () {
    $service = new GitHubLinkService('');
    $url = $service->generateIssueUrl(456);
    expect($url)->toBeNull();
});

it('can set repository after instantiation', function () {
    $service = new GitHubLinkService;
    $service->setRepository('new/repo');
    $url = $service->generatePullRequestUrl(123);
    expect($url)->toBe('https://github.com/new/repo/pull/123');
});
