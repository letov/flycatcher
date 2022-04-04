<?php

$container = require 'bootstrap.dev.php';

if (count($argv) != 2 || !$container->has($argv[1]))
{
    echo "Empty or incorrect shell download tool class name";
    die();
}

$shellToolClassName = $argv[1];

$worker = $container->make('DownloadToolWorker', array(
    'downloadTool' => $container->make($shellToolClassName, array(
        'argsSupport' => $container->make('ArgSupport', array(
            'args' =>  array(
                'Shell' => $container->get($shellToolClassName . ".shell")
            )
        ))
    ))
));
$worker->work();