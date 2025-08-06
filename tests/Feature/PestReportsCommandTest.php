<?php

use Illuminate\Support\Facades\File;

it('can generate a report', function () {
    $outputFile = __DIR__.'/../fixtures/report.html';

    // Clean up before test
    if (File::exists($outputFile)) {
        File::delete($outputFile);
    }

    $this->artisan('test-report:generate', [
        '--input' => __DIR__.'/../fixtures/output.json',
        '--output' => $outputFile,
    ])->assertExitCode(0);

    $this->assertFileExists($outputFile);

    $htmlContent = File::get($outputFile);
    expect($htmlContent)->toContain('Pest Test Results');
});
