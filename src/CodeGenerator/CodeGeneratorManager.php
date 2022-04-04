<?php

namespace Letov\Flycatcher\CodeGenerator;

class CodeGeneratorManager
{
    static public function generateAll(array $codeGenerators) {
        foreach ($codeGenerators as $codeGenerator) {
            $codeGenerator->generate();
        }
    }
}