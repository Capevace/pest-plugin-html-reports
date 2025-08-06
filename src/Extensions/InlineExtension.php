<?php

declare(strict_types=1);

namespace Mateffy\HtmlReports\Extensions;

use Mateffy\HtmlReports\Services\ReportGenerator;
use Mateffy\HtmlReports\Subscribers\TestRunnerFinishedSubscriber;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

/**
 * Registered in phpunit.xml
 */
final class InlineExtension implements Extension
{
	public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
	{
		$facade->replaceOutput();

		$facade->registerSubscribers(
			new TestRunnerFinishedSubscriber(new ReportGenerator()),
		);
	}
}
