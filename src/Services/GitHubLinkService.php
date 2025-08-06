<?php

declare(strict_types=1);

namespace Mateffy\HtmlReports\Services;

class GitHubLinkService
{
	private string $repository;
	private string $provider;

	public function __construct(string $repository = '', string $provider = 'GitHub')
	{
		$this->repository = $repository;
		$this->provider = $provider;
	}

	public function generatePullRequestUrl(int $prNumber): ?string
	{
		if (empty($this->repository)) {
			return null;
		}

		return "https://github.com/{$this->repository}/pull/{$prNumber}";
	}

	public function generateIssueUrl(int $issueNumber): ?string
	{
		if (empty($this->repository)) {
			return null;
		}

		return "https://github.com/{$this->repository}/issues/{$issueNumber}";
	}

	public function getProviderName(): string
	{
		return $this->provider;
	}

	public function getRepository(): string
	{
		return $this->repository;
	}

	public function setRepository(string $repository): void
	{
		$this->repository = $repository;
	}

	public function setProvider(string $provider): void
	{
		$this->provider = $provider;
	}
}
