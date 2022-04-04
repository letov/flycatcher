<?php

$container = require 'bootstrap.php';

$worker = $container->make('DownloadToolWorker', array(
    'downloadTool' => $container->make('PhantomJS', array(
        'argsSupport' => $container->make('ArgSupport', array(
            'args' =>  array(
                'Shell' => $container->get("Shell.phantomJS")
            )
        ))
    ))
));
$worker->work();