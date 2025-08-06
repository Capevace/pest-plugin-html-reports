<?php

use Mateffy\HtmlReports\DTOs\TestResultDTO;

it('can be created from an array', function () {
    $data = json_decode(file_get_contents(__DIR__.'/../fixtures/output_for_dto.json'), true);

    $dto = TestResultDTO::fromArray($data, __DIR__);

    expect($dto->counts->tests)->toBe(1);
    expect($dto->testSuites)->toHaveCount(1);
});
