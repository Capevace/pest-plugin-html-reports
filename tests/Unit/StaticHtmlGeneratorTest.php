<?php

use Mateffy\HtmlReports\Services\StaticHtmlGenerator;
use Mateffy\HtmlReports\DTOs\TestResultDTO;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Spatie\LaravelMarkdown\MarkdownRenderer;

it('can generate html with minimal data', function () {
	$testResultDto = TestResultDTO::fromArray([
		'counts' => [],
		'testSuites' => [],
		'failed' => [],
	]);

	$view = Mockery::mock(View::class);
	$view->shouldReceive('render')->andReturn('<html></html>');

	$viewFactory = Mockery::mock(Factory::class);
	$viewFactory->shouldReceive('make')->andReturn($view);

	$generator = new StaticHtmlGenerator(viewFactory: $viewFactory);

	$html = $generator->generateHtml($testResultDto);

	expect($html)->toBe('<html></html>');
});

it('can generate html from array', function () {
	$data = [
		'counts' => [],
		'testSuites' => [],
		'failed' => [],
	];

	$view = Mockery::mock(View::class);
	$view->shouldReceive('render')->andReturn('<html></html>');

	$viewFactory = Mockery::mock(Factory::class);
	$viewFactory->shouldReceive('make')->andReturn($view);

	$generator = new StaticHtmlGenerator(viewFactory: $viewFactory);

	$html = $generator->generateHtmlFromArray($data);

	expect($html)->toBe('<html></html>');
});

it('can generate html from json', function () {
	$json = '{ "counts": [], "testSuites": [], "failed": [] }';

	$view = Mockery::mock(View::class);
	$view->shouldReceive('render')->andReturn('<html></html>');

	$viewFactory = Mockery::mock(Factory::class);
	$viewFactory->shouldReceive('make')->andReturn($view);

	$generator = new StaticHtmlGenerator(viewFactory: $viewFactory);

	$html = $generator->generateHtmlFromJson($json);

	expect($html)->toBe('<html></html>');
});

it('throws exception for invalid json', function () {
	$json = '{ invalid json }';

	$viewFactory = Mockery::mock(Factory::class);
	$generator = new StaticHtmlGenerator(viewFactory: $viewFactory);

	$generator->generateHtmlFromJson($json);
})->throws(\InvalidArgumentException::class, 'Invalid JSON data provided');

it('renders markdown in test notes', function () {
	$data = json_decode(file_get_contents(__DIR__ . '/../fixtures/output_with_notes.json'), true);
	$testResultDto = TestResultDTO::fromArray($data);

	$markdownRenderer = Mockery::mock(MarkdownRenderer::class);
	$markdownRenderer->shouldReceive('toHtml')->andReturn('rendered_note');

	$view = Mockery::mock(View::class);
	$view->shouldReceive('render')->andReturn('<html></html>');

	$viewFactory = Mockery::mock(Factory::class);
	$viewFactory->shouldReceive('make')->andReturn($view);

	$generator = new StaticHtmlGenerator(viewFactory: $viewFactory, markdownRenderer: $markdownRenderer);

	$html = $generator->generateHtml($testResultDto);

	expect($html)->toBe('<html></html>');

	$markdownRenderer->shouldHaveReceived('toHtml')->with('**Bold Note**');
});
