<?php

declare(strict_types=1);

use SaltyWars\Launcher\ExtensionHost;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/lib.php';

function main(): int
{
    enableErrorHandler();

    $options = getopt('', ['help::']);
    if ($options === false) {
        return 1;
    }

    if (isset($options['help'])) {
        writeln('help text');
        return 0;
    }

    writeln('Salty Wars');

    $xth = new ExtensionHost();
    $xth->loadExtensions(__DIR__ . '/../extensions');

    try {
        $xth->bootstrap();
    } catch (Exception $ex) {
        writeln('Bootstrapping failed: ', $ex->getMessage());
        return 1;
    }

    return 0;
}
