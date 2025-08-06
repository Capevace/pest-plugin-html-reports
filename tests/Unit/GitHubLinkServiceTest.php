<?php

use Mateffy\HtmlReports\Services\GitHubLinkService;

it('can generate a pull request url', function () {
	$service = new GitHubLinkService('user/repo');

	$url = $service->generatePullRequestUrl(123);

	expect($url)->toBe('https://github.com/user/repo/pull/123');
});

it('can generate an issue url', function () {
	$service = new GitHubLinkService('user/repo');

	$url = $service->generateIssueUrl(456);

	expect($url)->toBe('https://github.com/user/repo/issues/456');
});
