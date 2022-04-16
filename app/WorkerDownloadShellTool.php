<?php
$container = require 'bootstrap.php';
$worker = $container->make('Worker.downloadToolWorker', array(
    'container' => $container,
));
$worker->workCycle();