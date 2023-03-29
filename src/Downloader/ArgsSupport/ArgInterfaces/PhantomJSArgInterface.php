<?php

declare(strict_types=1);

namespace Letov\Flycatcher\Downloader\ArgsSupport\ArgInterfaces;

interface PhantomJSArgInterface
{
    public function getPhantomJSConnector(): ?string;

    public function getPhantomJSViewportWidth(): ?int;

    public function getPhantomJSViewportHeight(): ?int;

    public function getPhantomJSSnapshotSelector(): ?string;

    public function getPhantomJSSnapshotPath(): ?string;

    public function getPhantomJSSaveContentPath(): ?string;

    public function getPhantomJSSaveContentMimeFilter(): ?array;

    public function getPhantomJSSaveContentWait(): ?int;

    public function getPhantomJSClickSelectorMap(): ?array;

    public function getPhantomJSClickSelectorMapRepeat(): ?int;
}
