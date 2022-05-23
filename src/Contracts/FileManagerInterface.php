<?php

namespace Braunstetter\MediaBundle\Contracts;

interface FileManagerInterface
{
    public function upload(FileInterface $fileEntity): void;
    public function getFolder(): string;
    public function setFolder(String $folder): FileManagerInterface;
}