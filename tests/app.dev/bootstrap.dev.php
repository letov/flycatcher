<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Letov\Flycatcher\CodeGenerator\CodeGeneratorInterface;
use Letov\Flycatcher\CodeGenerator\CodeGeneratorManager;
use Letov\Flycatcher\Downloader\ArgsSupport\ArgsSupportCodeGenerator;

require __DIR__.'/../../vendor/autoload.php';

if (!in_array(CodeGeneratorInterface::class, get_declared_interfaces(), true)) {
    CodeGeneratorManager::generateAll([
        new ArgsSupportCodeGenerator(),
    ]);
}

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__.'/config.dev.php');
$container = $builder->build();

$container->get('Directories')->initAppDirs(
    array_merge([$container->get('Directories.rootPath')], $container->get('Directories.paths'))
);

set_exception_handler(function ($e) use ($container): void {
    $lines = explode("\n", (string) $e);
    foreach ($lines as $line) {
        $container->get('Logger')->error($line);
    }

    exit;
});

return $container;
