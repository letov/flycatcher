<?php

namespace Letov\Flycatcher\DomParser\PhpHtmlParser;

use FastSimpleHTMLDom\Document;
use Letov\Flycatcher\DomParser\DomDocumentInterface;

class DomDocument implements DomDocumentInterface
{
    private Document $dom;

    public function loadFromFile($filePath): ?DomDocumentInterface
    {
        if (!file_exists($filePath)) {
            return null;
        } else {
            return $this->loadFromString(file_get_contents($filePath));
        }
    }

    public function loadFromString($html): ?DomDocumentInterface
    {
        $this->dom = Document::str_get_html($html);
        return $this;
    }

    public function find($selector): array
    {
        return (array)$this->dom->find($selector);
    }
}