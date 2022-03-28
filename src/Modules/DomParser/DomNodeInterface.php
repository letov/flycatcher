<?php

namespace Letov\Flycatcher\Modules\DomParser;

interface DomNodeInterface
{
    public function getAttribute($name): array;
    public function getText($name): ?string;
}