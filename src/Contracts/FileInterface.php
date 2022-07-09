<?php

namespace Braunstetter\MediaBundle\Contracts;

use SplFileInfo;

interface FileInterface
{
    public function getFilename();
    public function getOriginalFilename();
    public function getMimeType();
    public function getType();
    public function getFile(): ?SplFileInfo;
    public function getFullPath(): string|null;

    public function setFilename(string $filename);
    public function setOriginalFilename(string $originalFilename): self;
    public function setMimeType(string $mimeType): self;
    public function setFile(?SplFileInfo $file): void;
    public function setFolder(string $folder);
    public function hasFile(): bool;

    public function __toString();
}