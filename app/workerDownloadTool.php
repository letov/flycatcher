<?php
$container = require 'bootstrap.php';
$worker = $container->make('Worker.downloadTool', array(
    'container' => $container,
));
$worker->workCycle();