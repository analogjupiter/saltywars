<?php

declare(strict_types=1);

function writeln(string...$s)
{
    foreach ($s as $v) {
        echo $v;
    }
    echo PHP_EOL;
}

function enableErrorHandler()
{
    set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    });
}
