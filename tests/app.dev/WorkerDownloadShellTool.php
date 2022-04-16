<?php
$container = require 'bootstrap.dev.php';
$worker = $container->make('Worker.downloadToolWorker', array(
    'container' => $container,
));
$worker->workCycle();