<?php

namespace Mateffy\HtmlReports\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Mateffy\HtmlReports\DTOs\TestMethodDTO;
use Mateffy\HtmlReports\DTOs\TestResultDTO;
use Mateffy\HtmlReports\DTOs\TestSuiteDTO;
use Mateffy\HtmlReports\Jobs\RunAgentStep;
use Mateffy\HtmlReports\Services\AgentService;
use Mateffy\HtmlReports\Services\LinearIssueService;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\spin;

class RunAgent
{
	public function __invoke(Request $request)
	{
		try {
			if (!app()->isLocal()) {
				return abort(404);
			}

			$id = $request->json('id');
			$testData = $request->json('test');

			if (!$testData || !is_array($testData) || !is_string($id)) {
				return response()->json([
					'error' => 'Invalid test data or id',
				], 400);
			}

			if ($report = cache()->get(AgentService::makeKey($id))) {
				return response()->json([
					'report' => $report,
				], 200);
			}

			$result = app(AgentService::class)->run($id, $testData);


			cache()->put(AgentService::makeKey($id), $result, now()->addHour());

			return response()->json([
				'report' => $result,
			]);
		} catch (\Throwable $e) {
			return response()->json([
				'error' => $e->getMessage(),
			], 500);
		}
	}
}
