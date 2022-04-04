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
}