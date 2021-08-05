<?php

declare(strict_types=1);

use PhpCfdi\CfdiCleaner\Cleaner;

require __DIR__ . '/bootstrap.php';

exit(call_user_func(function (string $command, string ...$arguments): int {
    if (count(array_intersect($arguments, ['-h', '--help'])) > 0) {
        echo implode(PHP_EOL, [
            basename($command) . ' [-h|--help] cfdi.xml',
            '  -h, --help   Show this help',
            '  cfdi.xml     File to check',
            '  WARNING: This program can change at any time! Do not depend on this file or its results!',
            '',
        ]);
        return 0;
    }
    try {
        $filename = $arguments[0];
        if (! file_exists($filename)) {
            throw new Exception("File $filename does not exists");
        }
        echo Cleaner::staticClean(file_get_contents($filename) ?: '');
        return 0;
    } catch (Throwable $exception) {
        file_put_contents('php://stderr', $exception->getMessage() . PHP_EOL, FILE_APPEND);
        return 1;
    }
}, ...$argv));
