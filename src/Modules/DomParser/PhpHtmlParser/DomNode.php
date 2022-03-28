<?php

namespace Letov\Flycatcher\Modules\DomParser\PhpHtmlParser;

use FastSimpleHTMLDom\Element;
use Letov\Flycatcher\Modules\DomParser\DomNodeInterface;

class DomNode implements DomNodeInterface
{
    private Element $node;

    public function __construct($node)
    {
        $this->node = $node;
    }

    public function getAttribute($name): array
    {
        return (array)$this->node->getAttribute($name);
    }

    public function getText($name): ?string
    {
        return $this->node->text();
    }
}