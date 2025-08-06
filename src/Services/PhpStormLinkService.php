<?php

declare(strict_types=1);

namespace Mateffy\HtmlReports\Services;

class PhpStormLinkService
{
	private array $availableEditors = [
		'phpstorm' => 'PhpStorm',
		'vscode' => 'VS Code',
		'sublime' => 'Sublime Text',
		'vim' => 'Vim',
	];

	private string $selectedEditor = 'phpstorm';
	private string $projectPath = '';

	public function __construct(string $projectPath = '', string $selectedEditor = 'phpstorm')
	{
		$this->projectPath = $projectPath;
		$this->selectedEditor = $selectedEditor;
	}

	public function generateDeepLink(string $filePath, int $lineNumber, string $editor = null): string
	{
		$editor = $editor ?: $this->selectedEditor;
		$relativePath = $this->getRelativePath($filePath);

		return match ($editor) {
			'phpstorm' => "phpstorm://open?file={$relativePath}&line={$lineNumber}",
			'vscode' => "vscode://file/{$relativePath}:{$lineNumber}",
			'sublime' => "subl://{$relativePath}:{$lineNumber}",
			'vim' => "vim://{$relativePath}:{$lineNumber}",
			default => "phpstorm://open?file={$relativePath}&line={$lineNumber}",
		};
	}

	public function getAvailableEditors(): array
	{
		return $this->availableEditors;
	}

	public function getSelectedEditor(): string
	{
		return $this->selectedEditor;
	}

	public function getProjectPath(): string
	{
		return $this->projectPath;
	}

	public function setSelectedEditor(string $editor): void
	{
		$this->selectedEditor = $editor;
	}

	public function setProjectPath(string $projectPath): void
	{
		$this->projectPath = $projectPath;
	}

	private function getRelativePath(string $filePath): string
	{
		if (empty($this->projectPath)) {
			return $filePath;
		}

		return str_replace($this->projectPath . '/', '', $filePath);
	}
}
