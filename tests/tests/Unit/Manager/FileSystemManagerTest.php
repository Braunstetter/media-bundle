<?php

namespace Braunstetter\MediaBundle\Tests\Unit\Manager;

use App\Entity\Media\Image;
use Braunstetter\MediaBundle\Tests\Functional\AbstractMediaBundleTestCase;
use Braunstetter\MediaBundle\Tests\TestHelper;
use Braunstetter\MediaBundle\Uploader\FilesystemUploader;
use Braunstetter\MediaBundle\Tests\AbstractBaseTestCase;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\String\Slugger\AsciiSlugger;

class FileSystemManagerTest extends AbstractMediaBundleTestCase
{

    public function test_upload_manager_works_but_does_nothing_when_file_is_not_set_on_entity()
    {
        $fileEntity = new Image();

        // no file is set yet - so nothing to upload
        $this->assertNull($fileEntity->getFile());
        $this->assertFalse($this->uploader->upload($fileEntity));
    }

    public function test_upload_does_actually_upload_images()
    {
        $fileEntity = TestHelper::createImageEntity('person.jpg');

        $this->uploader->setFolder(AbstractMediaBundleTestCase::FOLDER);
        $this->uploader->upload($fileEntity);

        // after an upload the file property is empty again
        $this->assertNull($fileEntity->getFile());

        // file has set important properties
        $this->assertNotNull($fileEntity->getFilename());
        $this->assertNotNull($fileEntity->getFullPath());
    }

    public function test_file_gets_removed_if_exists_already()
    {
        $entity = TestHelper::createImageEntity('person.jpg');
        $this->uploader->setFolder(AbstractMediaBundleTestCase::FOLDER);
        $this->uploader->upload($entity, false);
        $this->assertTrue($this->fileSystem->exists(TestHelper::getPublicDir() . $entity->getFullPath()));

        $entity->setFile(TestHelper::createImage('person.jpg'));
        $this->uploader->upload($entity, false);
        $this->assertTrue($this->fileSystem->exists(TestHelper::getPublicDir() . $entity->getFullPath()));
    }

}