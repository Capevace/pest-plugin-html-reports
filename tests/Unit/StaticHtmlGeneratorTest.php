<?php

use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Mateffy\HtmlReports\DTOs\TestResultDTO;
use Mateffy\HtmlReports\Services\GitHubLinkService;
use Mateffy\HtmlReports\Services\PhpStormLinkService;
use Mateffy\HtmlReports\Services\StaticHtmlGenerator;
use Spatie\LaravelMarkdown\MarkdownRenderer;

beforeEach(function () {
    $this->view = Mockery::mock(View::class);
    $this->view->shouldReceive('render')->andReturn('<html></html>');

    $this->viewFactory = Mockery::mock(Factory::class);
    $this->viewFactory->shouldReceive('make')->andReturn($this->view);

    $this->gitHubService = Mockery::mock(GitHubLinkService::class);
    $this->gitHubService->shouldReceive('setRepository')->andReturnSelf();

    $this->phpStormService = Mockery::mock(PhpStormLinkService::class);
    $this->phpStormService->shouldReceive('setProjectPath')->andReturnSelf();
    $this->phpStormService->shouldReceive('setSelectedEditor')->andReturnSelf();
    $this->phpStormService->shouldReceive('getAvailableEditors')->andReturn(['phpstorm' => 'PhpStorm']);

    $this->markdownRenderer = Mockery::mock(MarkdownRenderer::class);
});

it('can generate html with minimal data', function () {
    $testResultDto = TestResultDTO::fromArray([
        'counts' => [],
        'testSuites' => [],
    ], __DIR__);

    $generator = new StaticHtmlGenerator(
        $this->gitHubService,
        $this->phpStormService,
        $this->markdownRenderer,
        $this->viewFactory
    );

    $html = $generator->generateHtml($testResultDto);

    expect($html)->toBe('<html></html>');
});

it('can generate html from array', function () {
    $data = [
        'counts' => [],
        'testSuites' => [],
    ];

    $generator = new StaticHtmlGenerator(
        $this->gitHubService,
        $this->phpStormService,
        $this->markdownRenderer,
        $this->viewFactory
    );

    $html = $generator->generateHtmlFromArray($data);

    expect($html)->toBe('<html></html>');
});

it('can generate html from json', function () {
    $json = '{ "counts": [], "testSuites": [] }';

    $generator = new StaticHtmlGenerator(
        $this->gitHubService,
        $this->phpStormService,
        $this->markdownRenderer,
        $this->viewFactory
    );

    $html = $generator->generateHtmlFromJson($json);

    expect($html)->toBe('<html></html>');
});

it('throws exception for invalid json', function () {
    $json = '{ invalid json }';

    $generator = new StaticHtmlGenerator(
        $this->gitHubService,
        $this->phpStormService,
        $this->markdownRenderer,
        $this->viewFactory
    );

    $generator->generateHtmlFromJson($json);
})->throws(\InvalidArgumentException::class, 'Invalid JSON data provided');

it('renders markdown in test notes', function () {
    $data = json_decode(file_get_contents(__DIR__.'/../fixtures/output_with_notes.json'), true);
    $testResultDto = TestResultDTO::fromArray($data, __DIR__);

    $this->markdownRenderer->shouldReceive('toHtml')->andReturn('rendered_note');

    $generator = new StaticHtmlGenerator(
        $this->gitHubService,
        $this->phpStormService,
        $this->markdownRenderer,
        $this->viewFactory
    );

    $html = $generator->generateHtml($testResultDto);

    expect($html)->toBe('<html></html>');

    $this->markdownRenderer->shouldHaveReceived('toHtml')->with('**Bold Note**');
});
