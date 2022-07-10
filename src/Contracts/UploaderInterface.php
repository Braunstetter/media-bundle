<?php

namespace Braunstetter\MediaBundle\Contracts;

interface UploaderInterface
{
    public function upload(FileInterface $fileEntity, bool $uniqFileName = true): bool;

    public function getFolder(): string;

    public function setFolder(string $folder): UploaderInterface;
}