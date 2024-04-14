<?php

use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;

test('it can run the cli script to sync the database', function () {
    Process::fake();

    config(['database.connections.turso.db_replica' => '/tmp/turso.sqlite']);

    Artisan::call('turso:sync');

    Process::assertRan(function (PendingProcess $process) {
        $expectedPath = realpath(__DIR__ . '/../..');

        expect($process->command)->toBe('node turso-sync.mjs "http://127.0.0.1:8080" "your-access-token" "/tmp/turso.sqlite"')
            ->and($process->timeout)->toBe(60)
            ->and($process->path)->toBe($expectedPath);

        return true;
    });
})->group('TursoSyncCommandTest', 'UnitTest');

test('it can handle process error output', function () {
    Process::fake([
        '*' => Process::result(
            output: 'Whooops! Something went wrong!',
            errorOutput: 'Error: Something went wrong!',
            exitCode: 500
        ),
    ]);

    config(['database.connections.turso.db_replica' => '/tmp/turso.sqlite']);

    $result = Artisan::call('turso:sync');

    expect($result)->toBe(1);
})->group('TursoSyncCommandTest', 'UnitTest');
