<?php

declare(strict_types=1);
$container = require 'bootstrap.php';
$worker = $container->make('Worker.downloadTool', [
    'container' => $container,
]);
$worker->workCycle();
