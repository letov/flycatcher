<?php

declare(strict_types=1);

namespace Letov\Flycatcher\DomParser;

interface DomDocumentInterface
{
    public function loadFromString($html): ?self;

    public function loadFromFile($filePath): ?self;

    public function find($selector): array;
}
