<?php

use Fureev\Loogger\Config;
use Fureev\Loogger\Loogger;

/**
 * @arguments
 *  `--cnf=<path>`
 *  `--msg='<text>'`
 *  `--debug`
 */
array_shift($argv);

$cnf = __DIR__ . '/.loogger.json';

foreach ($argv as $arg) {
    $list = explode('=', $arg);
    $k    = $list[0];
    $v    = count($list) === 2 ? $v = $list[1] : true;

    if (!str_starts_with($k, '--')) {
        continue;
    }

    $key  = str_replace('--', '', $k);
    $$key = $v;
}


if (!$looggerFile = \file_get_contents($cnf)) {
    throw new RuntimeException("Logger`s config file not found: $cnf");
}

if (!$looggerConfigJson = \json_decode($looggerFile, true, 512, JSON_THROW_ON_ERROR)) {
    throw new RuntimeException('Logger`s config file is not JSON');
}

$looggerConfigJson['debug'] = $debug ?? $looggerConfigJson['debug'] ?? false;

require_once __DIR__ . '/src/Config.php';
require_once __DIR__ . '/src/Loogger.php';

$loogger = new Loogger(new Config($looggerConfigJson));

try {
    if (!isset($msg) || $msg === '') {
        echo "[ERR] A message test is empty!\n";
        die(1);
    }

    $loogger
        ->debug($debug ?? false)
        // ->pattern("<b>Warning!</b>: {{MSG}}")
        // ->asHTML()
        ->sendDebug($msg);
} catch (Exception $e) {
    $loogger
        ->pattern("<b>Error!</b>: An error occurred{{BR}}{{MSG}}")
        ->sendHTML($e->getMessage());
    echo "[ERR] aborting...\n";
    die(-1);
}
