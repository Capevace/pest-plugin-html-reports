<?php

namespace Mateffy\HtmlReports\Services;

use Mateffy\HtmlReports\DTOs\LinearIssue;
use Mateffy\HtmlReports\DTOs\TestMethodDTO;
use Prism\Prism\Prism;
use Prism\Prism\Schema\ObjectSchema;
use Prism\Prism\Schema\StringSchema;

class LinearIssueService
{
	public function createIssue(array $testData): LinearIssue
	{
		$systemPrompt = <<<'PROMPT'
			You are a Laravel expert that has to write a professional Linear issue based on the Pest test results of a single test.
			Include enough detail so that a developer or LLM can work on and fix the issue.

			Output a title and description for the Linear issue.
		PROMPT;

		$json = json_encode($testData, JSON_PRETTY_PRINT);

		$data = Prism::structured()
			->using('gemini', 'gemini-2.5-flash-lite')
			->withSchema(
				new ObjectSchema(
					name: 'LinearIssue',
					description: 'A Linear issue',
					properties: [
						new StringSchema(
							name: 'title',
							description: 'The title of the Linear issue',
						),
						new StringSchema(
							name: 'description',
							description: 'The description of the Linear issue. Use markdown formatting and multi-line paragraphs.',
						),
					],
					requiredFields: [
						'title',
						'description',
					],
				)
			)
			->withSystemPrompt($systemPrompt)
			->withPrompt(<<<PROMPT
				<test-results>
				$json
				</test-results>
			PROMPT)
			->asStructured()
			->structured;

		return LinearIssue::fromArray($data);
	}
}
