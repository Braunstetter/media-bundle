<?php

namespace Braunstetter\MediaBundle\Contracts;

interface FileManagerInterface
{
    public function upload(FileInterface $fileEntity,  bool $uniqFileName = true): bool;
    public function getFolder(): string;
    public function setFolder(String $folder): FileManagerInterface;
}