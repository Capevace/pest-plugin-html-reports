<?php

namespace Mateffy\HtmlReports\Services;

use Illuminate\Support\Facades\Process;

class AgentService
{
	public static function makeKey(string $id): string
	{
		return "pest-reports::reports::{$id}";
	}

	protected function system()
	{
		return <<<'PROMPT'
		You are a Laravel developer looking at a Pest test case.
		You are given a test case and a test result.

		Analyze what the best thing to do is in relation to the content of the test case and the test result.
		Fix broken tests, improve test cases, add more tests, etc.
		It is also perfectly fine to do nothing in which case you should just return "Nothing to do".
		PROMPT;
	}


	public function prompt(array $test)
	{
		$json = json_encode($test, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);

		return <<<PROMPT
		<instructions>
			You are a Laravel developer looking at a Pest test case.
			You are given a test case and a test result.

			Analyze what the best thing to do is in relation to the content of the test case and the test result.
			Fix broken tests, improve test cases, add more tests, etc.
		
			It is also perfectly fine to do nothing in which case you should just return "Nothing to do".
		</instructions>

		<task>Analyze the following Pest test case and respond accordingly.</task>

		<test-results>
		$json
		</test-results>
		PROMPT;
	}

	public function run(string $id, array $test): array
	{
		$system = $this->system();
		$prompt = $this->prompt($test);

		// Encode the prompts for the command line (escape quotes, newlines, etc.)
		$prompt = escapeshellarg($prompt);
		$system = escapeshellarg($system);

		$command = "/Users/mat/.bun/bin/claude --output-format=stream-json --print --model sonnet --append-system-prompt $system $prompt";
		// Ensure Node is available on PATH for the Claude Code CLI's node-based entrypoint
		$nodeBinDir = '/Users/mat/Library/Application Support/Herd/config/nvm/versions/node/v22.16.0/bin';
		$envPath = $nodeBinDir . ':' . getenv('PATH');
		$process = Process::env(['PATH' => $envPath])->start($command);

		$output = '';
		$result = $process->wait(function ($type, $line) use (&$output, $id) {
			$output .= $line;

			dd($line);

			cache()->put(self::makeKey($id), [
				'status' => 'processing',
				'output' => $output,
			], now()->addHour());
		});

		if ($result->failed()) {
			cache()->put(self::makeKey($id), [
				'status' => 'failed',
				'output' => $result->output(),
				'error' => $result->errorOutput(),
			], now()->addHour());

			return [
				'status' => 'failed',
				'output' => $result->output(),
			];
		}


		cache()->put(self::makeKey($id), [
			'status' => 'completed',
			'output' => $result->output(),
		], now()->addHour());

		return [
			'status' => 'completed',
			'output' => $result->output(),
		];
	}
}
