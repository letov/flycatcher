<?php

namespace Letov\Flycatcher\DomParser;

interface DomNodeInterface
{
    public function getAttribute($name): array;

    public function getText(): ?string;
}