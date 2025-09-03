<?php

declare(strict_types=1);

namespace Mateffy\HtmlReports\Services;

use Illuminate\Contracts\View\Factory;
use Mateffy\HtmlReports\DTOs\TestResultDTO;
use Spatie\LaravelMarkdown\MarkdownRenderer;

class StaticHtmlGenerator
{
	public function __construct(
		protected readonly GitHubLinkService $gitHubService,
		protected readonly PhpStormLinkService $phpStormService,
		protected readonly Factory $viewFactory
	) {}

	public function generateHtml(TestResultDTO $testResults, array $options = []): string
	{
		$options = array_merge([
			'title' => 'Pest Test Results',
			'projectPath' => '',
			'selectedEditor' => 'phpstorm',
			'gitHubRepository' => '',
		], $options);

		// Configure services
		$this->phpStormService->setProjectPath($options['projectPath']);
		$this->phpStormService->setSelectedEditor($options['selectedEditor']);
		$this->gitHubService->setRepository($options['gitHubRepository']);

		$testSuites = [];
		foreach ($testResults->testSuites as $suite) {
			$tests = [];
			foreach ($suite->tests as $test) {
				$testData = $test->toArray();
				if (isset($testData['notes']) && is_array($testData['notes'])) {
					$testData['notes'] = array_map(fn($note) => (new MarkdownRenderer())->toHtml($note), $testData['notes']);
				}
				$tests[$test->name] = $testData;
			}
			$testSuites[] = [
				'description' => $suite->description,
				'tests' => $tests,
			];
		}

		$viewData = [
			'title' => $options['title'],
			'counts' => $testResults->counts->toArray(),
			'testSuites' => $testSuites,
			'gitHubService' => $this->gitHubService,
			'phpStormService' => $this->phpStormService,
			'selectedEditor' => $options['selectedEditor'],
			'availableEditors' => $this->phpStormService->getAvailableEditors(),
		];

		return $this->viewFactory->make('pest-reports::test-results', $viewData)->render();
	}

	public function generateHtmlFromArray(array $testResults, array $options = []): string
	{
		$dto = TestResultDTO::fromArray($testResults, $options['projectPath'] ?? null);

		return $this->generateHtml($dto, $options);
	}

	public function generateHtmlFromJson(string $jsonData, array $options = []): string
	{
		$data = json_decode($jsonData, true, flags: JSON_THROW_ON_ERROR);

		return $this->generateHtmlFromArray($data, $options);
	}
}
