<?php

namespace Letov\Flycatcher\Modules\DomParser;

interface DomDocumentInterface
{
    public function loadFromString($html): ?DomDocumentInterface;
    public function loadFromFile($filePath): ?DomDocumentInterface;
    public function find($selector): array;
}