<?php

namespace Letov\Flycatcher\Spyder;

class JsonUrlTree implements JsonUrlTreeInterface
{
    private array $storage;
    private ?string $jsonFilePath;
    private ?string $root;

    public function __construct(?string $jsonFilePath)
    {
        $this->jsonFilePath = $jsonFilePath;
    }

    public function setRoot(?string $root)
    {
        $this->root = $root;
    }

    public function add(string $url)
    {
        if(!empty($this->jsonFilePath))
        {
            $this->storage[] = $url;
        }
    }

    public function save()
    {
        if(!empty($this->jsonFilePath))
        {
            $json = [];
            foreach ($this->storage as $url) {
                $arr = $this->compilePathArr($url);
                $json = array_merge_recursive($json, $arr);
                @file_put_contents($this->jsonFilePath, json_encode($json));
            }
        }
    }

    private function compilePathArr(string $url): array
    {
        $urlParts = parse_url($url);
        $pathParts = explode('/', $urlParts['path']);
        if (isset($urlParts['query']))
        {
            $pathParts[] = '?' . $urlParts['query'];
        }
        if (isset($urlParts['fragment']))
        {
            $pathParts[] = '#' . $urlParts['fragment'];
        }
        $pathParts = array_reverse($pathParts);
        $path = [];
        $beforePart = "";
        foreach ($pathParts as $pathPart) {
            if (!empty(trim($pathPart))) {
                $path[$pathPart] = $path;
                if (isset($path[$beforePart])) {
                    unset($path[$beforePart]);
                }
                $beforePart = $pathPart;
            }
        }
        if (!empty($this->root)) {
            return array(
                $this->root => $path
            );
        } else {
            return $path;
        }
    }
}