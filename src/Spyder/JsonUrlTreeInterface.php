<?php

namespace Letov\Flycatcher\Spyder;

interface JsonUrlTreeInterface
{
    public function __construct(?string $jsonFilePath);

    public function setRoot(?string $root);

    public function add(string $url);

    public function save();
}