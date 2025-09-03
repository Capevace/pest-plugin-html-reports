<?php

namespace Mateffy\HtmlReports\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Mateffy\HtmlReports\DTOs\TestMethodDTO;
use Mateffy\HtmlReports\DTOs\TestResultDTO;
use Mateffy\HtmlReports\DTOs\TestSuiteDTO;
use Mateffy\HtmlReports\Services\LinearIssueService;

class GenerateIssue
{
	public function __invoke(Request $request)
	{
		if (!app()->isLocal()) {
			return abort(404);
		}

		$testData = $request->json('test');

		if (!$testData || !is_array($testData)) {
			return response()->json([
				'error' => 'Invalid test data',
			], 400);
		}

		$linearIssue = app(LinearIssueService::class)->createIssue($testData);

		return response()->json([
			'issue' => $linearIssue->toArray(),
		]);
	}
}
