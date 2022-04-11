<?php

namespace Letov\Flycatcher\Downloader\ArgsSupport\ArgInterfaces;

use JonnyW\PhantomJs\ClientInterface;

interface PhantomJSArgInterface
{
    public function getPhantomJSConnector(): ?string;

    public function getPhantomJSViewportWidth(): ?int;

    public function getPhantomJSViewportHeight(): ?int;

    public function getPhantomJSSnapshotSelector(): ?string;

    public function getPhantomJSSnapshotPath(): ?string;

    public function getPhantomJSClient(): ?ClientInterface;

    public function getPhantomJSPath(): ?string;

    public function getPhantomJSSaveContentPath(): ?string;

    public function getPhantomJSSaveContentMimeFilter(): ?array;

    public function getPhantomJSSaveContentWait(): ?int;

    public function getPhantomJSClickSelectorMap(): ?array;

    public function getPhantomJSClickSelectorMapRepeat(): ?int;
}