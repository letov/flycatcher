<?php

namespace Letov\Flycatcher\Modules\DomParser\PhpHtmlParser;

use FastSimpleHTMLDom\Document;
use Letov\Flycatcher\Modules\DomParser\DomDocumentInterface;

class DomDocument implements DomDocumentInterface
{
    private Document $dom;

    public function loadFromString($html): ?DomDocumentInterface
    {
        $this->dom = Document::str_get_html($html);
        return $this;
    }

    public function loadFromFile($filePath): ?DomDocumentInterface
    {
        if (!file_exists($filePath)) {
            return null;
        } else {
            return $this->loadFromString(file_get_contents($filePath));
        }
    }

    public function find($selector): array
    {
        return (array)$this->dom->find($selector);
    }
}