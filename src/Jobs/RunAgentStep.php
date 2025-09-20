<?php

namespace Mateffy\HtmlReports\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Process;
use Mateffy\HtmlReports\Services\AgentService;

class RunAgentStep implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	public function __construct(public readonly string $id, public readonly array $test) {}

	public function handle(AgentService $agentService)
	{
		$result = $agentService->run($this->id, $this->test);
	}
}
