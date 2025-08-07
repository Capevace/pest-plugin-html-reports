<?php

declare(strict_types=1);

namespace Mateffy\HtmlReports\Services;

use Mateffy\HtmlReports\DTOs\TestResultDTO;
use Pest\Factories\TestCaseMethodFactory;
use Pest\TestSuite;
use PHPUnit\Event\Code\TestDox;
use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\Test\Errored;
use PHPUnit\Event\Test\Failed;
use PHPUnit\Event\Test\Skipped;
use PHPUnit\Event\TestSuite\Skipped as TestSuiteSkipped;
use PHPUnit\Metadata\DataProvider;
use PHPUnit\TestRunner\TestResult\TestResult;

use function Mateffy\HtmlReports\Support\invade;

class ReportGenerator
{
    public function generate(TestResult $testResult): TestResultDTO
    {
        $resultJsonData = [
            'counts' => [
                'tests' => $testResult->numberOfTestsRun(),
                'failed' => $testResult->numberOfTestFailedEvents(),
                'assertions' => $testResult->numberOfAssertions(),
                'errors' => $testResult->numberOfTestErroredEvents(),
                'warnings' => $testResult->numberOfWarnings(),
                'deprecations' => $testResult->numberOfDeprecations(),
                'notices' => $testResult->numberOfNotices(),
                'success' => $testResult->numberOfTestsRun() - $testResult->numberOfTestErroredEvents() - $testResult->numberOfTestFailedEvents(),
                'incomplete' => $testResult->numberOfTestMarkedIncompleteEvents(),
                'risky' => $testResult->numberOfTestsWithTestConsideredRiskyEvents(),
                'skipped' => $testResult->numberOfTestSuiteSkippedEvents() + $testResult->numberOfTestSkippedEvents(),
            ],
        ];

        $resultJsonData['failed'] = $this->createFailedEventDatas($testResult);

        $skippedEvents = collect($testResult->testSkippedEvents());
        $errorEvents = collect($testResult->testErroredEvents());
        $failedEvents = collect($testResult->testFailedEvents());

        $first = fn ($events, string $className, string $methodName) => $events
            ->first(function ($testEvent) use ($className, $methodName) {
                $test = invade($testEvent->test());
                /** @var TestDox $dox */
                $dox = $test->testDox();

                $otherClassName = $dox->prettifiedClassName();
                $otherMethodName = $dox->prettifiedMethodName();

                return $className === $otherClassName && $methodName === $otherMethodName;
            });

        $isSkipped = fn (string $className, string $methodName): Skipped|TestSuiteSkipped|null => $first($skippedEvents, $className, $methodName);
        $isErrored = fn (string $className, string $methodName): ?Errored => $first($errorEvents, $className, $methodName);
        $isFailed = fn (string $className, string $methodName): ?Failed => $first($failedEvents, $className, $methodName);

        $testSuites = collect((TestSuite::getInstance())->tests->getFilenames())
            ->mapWithKeys(function (string $filename) use ($isSkipped, $isErrored, $isFailed) {
                $case = TestSuite::getInstance()->tests->get($filename);

                $title = collect($case->methods)
                    ->first(fn (TestCaseMethodFactory $testCaseMethod) => $testCaseMethod->description)
                    ?->description ?? uniqid();

                $title = str($title)->before('→')->trim()->toString();

                return [
                    $title => array_filter([
                        'tests' => collect($case->methods)
                            ->values()
                            ->collect()
                            ->mapWithKeys(function (TestCaseMethodFactory $testCaseMethod) use ($isSkipped, $isErrored, $isFailed, $filename, $title) {
                                if (empty($testCaseMethod->description)) {
                                    return [];
                                }

                                $description = str($testCaseMethod->description)
                                    ->after('→')
                                    ->trim()
                                    ->toString();

                                $error = $isErrored($filename, $testCaseMethod->description);
                                $failed = $isFailed($filename, $testCaseMethod->description);

                                return [
                                    $description => array_filter([
                                        'description' => $title,
                                        'test' => $description,
                                        'filePath' => $filename,
                                        'issues' => ! empty($testCaseMethod->issues)
                                            ? $testCaseMethod->issues
                                            : null,
                                        'todo' => ! empty($testCaseMethod->todo)
                                            ? $testCaseMethod->todo
                                            : null,
                                        'assignees' => ! empty($testCaseMethod->assignees)
                                            ? $testCaseMethod->assignees
                                            : null,
                                        'prs' => ! empty($testCaseMethod->prs)
                                            ? $testCaseMethod->prs
                                            : null,
                                        'notes' => ! empty($testCaseMethod->notes)
                                            ? $testCaseMethod->notes
                                            : null,
                                        'skipped' => $isSkipped($filename, $testCaseMethod->description) !== null,
                                        'error' => $error
                                            ? [
                                                'message' => $error->throwable()->message(),
                                                'exception_class' => $error->throwable()->className(),
                                                'line' => $this->resolveLineNumber($error->throwable()->stackTrace()),
                                            ]
                                            : null,
                                        'failure' => $failed
                                            ? [
                                                'message' => $failed->throwable()->message(),
                                                'exception_class' => $failed->throwable()->className(),
                                                'line' => $this->resolveLineNumber($failed->throwable()->stackTrace()),
                                            ]
                                            : null,
                                    ]),
                                ];
                            }),
                    ]),
                ];
            })
            ->toArray();

        $resultJsonData['testSuites'] = $testSuites;

        return TestResultDTO::fromArray($resultJsonData);
    }

    private function createDataProviderData(TestMethod $testMethod): array
    {
        $dataFromDataProvider = $testMethod->testData()->dataFromDataProvider();

        $dataProviderData = [
            'key' => $dataFromDataProvider->dataSetName(),
            'data' => $dataFromDataProvider->data(),
        ];

        foreach ($testMethod->metadata() as $metadata) {
            if ($metadata instanceof DataProvider) {
                $dataProviderData['provider_method'] = $metadata->methodName();
            }
        }

        return $dataProviderData;
    }

    private function resolveLineNumber(string $stackTrace): int
    {
        preg_match('#:(?<line>\d+)$#', $stackTrace, $matches);

        if (! isset($matches['line'])) {
            return 0;
        }

        return (int) $matches['line'];
    }

    private function createFailedEventDatas(TestResult $testResult): array
    {
        $failedEventDatas = [];

        foreach ($testResult->testFailedEvents() as $testFailedEvent) {
            /** @var Failed $testFailedEvent */
            $testMethod = $testFailedEvent->test();

            /** @var TestMethod $testMethod */
            $failedEventData = [
                'test_file_path' => $testMethod->file(),
                'test_class' => $testMethod->className(),
                'test_method' => $testMethod->methodName(),
                'message' => $testFailedEvent->throwable()->message(),
                'exception_class' => $testFailedEvent->throwable()->className(),
                'line' => $this->resolveLineNumber($testFailedEvent->throwable()->stackTrace()),
            ];

            if ($testMethod->testData()->hasDataFromDataProvider()) {
                $failedEventData['data_provider'] = $this->createDataProviderData($testMethod);
            }

            $failedEventDatas[] = $failedEventData;
        }

        return $failedEventDatas;
    }
}
