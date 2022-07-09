<?php

namespace Braunstetter\MediaBundle\Tests;

use App\Entity\Media\Image;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypes;

class TestHelper
{

    public static function createImageEntity(string $name): Image
    {
        $fileEntity = new Image();
        $fileEntity->setFile(static::createImage($name));
        return $fileEntity;
    }

    public static function createImage(string $name): UploadedFile
    {
        $filePath = static::getAssetsDir() . '/images/' . $name;
        $fileTempPath = static::getImagesDir() . "/temp/" . $name;
        (new Filesystem())->copy($filePath, $fileTempPath);
        $mimeType = (new MimeTypes())->guessMimeType($filePath);
        return new UploadedFile($fileTempPath, $name, $mimeType, null, true);
    }

    public static function getProjectDir(): string
    {
        return realpath(dirname(__FILE__) . "/../app");
    }

    public static function getPublicDir(): string
    {
        return static::getProjectDir() . '/public';
    }

    public static function getAssetsDir(): string
    {
        return static::getProjectDir() . '/assets';
    }

    public static function getTestsDir(): string
    {
        return static::getPublicDir() . '/tests';
    }

    public static function getImagesDir(): string
    {
        return static::getTestsDir() . '/images';
    }

}