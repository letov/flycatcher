<?php

declare(strict_types=1);

namespace Letov\Flycatcher\CodeGenerator;

class CodeGeneratorManager
{
    public static function generateAll(array $codeGenerators): void
    {
        foreach ($codeGenerators as $codeGenerator) {
            $codeGenerator->generate();
        }
    }
}
