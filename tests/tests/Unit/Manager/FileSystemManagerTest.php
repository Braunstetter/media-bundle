<?php

namespace Braunstetter\MediaBundle\Tests\Unit\Manager;

use App\Entity\Media\Image;
use Braunstetter\MediaBundle\Manager\FilesystemManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\String\Slugger\AsciiSlugger;

class FileSystemManagerTest extends TestCase
{
    protected function setUp(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $slugger = new AsciiSlugger();

        $parameterBag = new ParameterBag();
        $parameterBag->set('kernel.project_dir', realpath($this->getProjectDir()));
        $this->fileSystem = new Filesystem();
        $this->fileManager = new FilesystemManager($logger, $this->fileSystem, $slugger, $parameterBag);
    }

    protected function tearDown(): void
    {
        $this->fileSystem->remove($this->getPublicDir());
    }

    public function test_upload_manager_works_but_does_nothing_when_file_is_not_set_on_entity()
    {
        $fileEntity = new Image();

        // no file is set yet - so nothing to upload
        $this->assertNull($fileEntity->getFile());
        $this->assertFalse($this->fileManager->upload($fileEntity));
    }

    public function test_upload_does_actually_upload_images()
    {
        $fileEntity = $this->createImageEntity('person.jpg');

        $this->fileManager->setFolder('/images');
        $this->fileManager->upload($fileEntity);

        // after an upload the file property is empty again
        $this->assertNull($fileEntity->getFile());

        // file has set important properties
        $this->assertNotNull($fileEntity->getFilename());
        $this->assertNotNull($fileEntity->getFullPath());
    }

    public function test_file_gets_removed_if_exists_already()
    {
        $entity = $this->createImageEntity('person.jpg');
        $this->fileManager->setFolder('/images');
        $this->fileManager->upload($entity, false);
        $this->assertTrue($this->fileSystem->exists($this->getPublicDir() . $entity->getFullPath()));

        $entity->setFile($this->createImage('person.jpg'));
        $this->fileManager->upload($entity, false);
        $this->assertTrue($this->fileSystem->exists($this->getPublicDir() . $entity->getFullPath()));
    }

    public function getProjectDir(): string
    {
        return dirname(__FILE__) . "/../../";
    }

    private function getPublicDir(): string
    {
        return realpath($this->getProjectDir()) . '/public';
    }

    private function createImage(string $name): UploadedFile
    {
        $filePath = $this->getProjectDir() . "../app/assets/images/" . $name;
        $fileTempPath = $this->getProjectDir() . "public/temp/" . $name;
        $this->fileSystem->copy($filePath, $fileTempPath);
        $mimeType = (new MimeTypes())->guessMimeType($filePath);
        return new UploadedFile($fileTempPath, $name, $mimeType, null, true);
    }

    private function createImageEntity(string $name): Image
    {
        $fileEntity = new Image();
        $fileEntity->setFile($this->createImage($name));
        return $fileEntity;
    }
}