<?php

namespace Braunstetter\MediaBundle\Tests\Functional\Filesystem;

use App\Entity\Media\Image;
use Braunstetter\MediaBundle\Tests\Functional\AbstractMediaBundleTestCase;
use Braunstetter\MediaBundle\Tests\TestHelper;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class UploaderTest extends AbstractMediaBundleTestCase
{

    public function test_the_test()
    {
        $client = new KernelBrowser($this->kernel);
        $client->request('GET', '/test');

        $crawler = $client->getCrawler();

        $form = $crawler->selectButton('form_submit')->form();
        $form['form[image][0][file]']->setValue(TestHelper::getAssetsDir() . '/images/ice.jpg');
        $client->submit($form);

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertCount(1, $client->getCrawler()->filter('form'));
    }

    public function testImageGetsRemovedWhenImageOfEntityChanges()
    {
        $entity = $this->getPersistedImageEntity();
        $oldFullPath = $entity->getFullPath();

        $newImage = TestHelper::createImage('ice.jpg');
        $entity->setFile($newImage);

        $this->uploader->upload($entity, false);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $this->assertFalse($this->fileSystem->exists(TestHelper::getPublicDir() . $oldFullPath));
    }

    public function testOriginalFilename()
    {
        $entity = $this->getPersistedImageEntity();
        $this->assertTrue($entity->getOriginalFilename() == 'person.jpg');
    }

    public function testMimeType()
    {
        $entity = $this->getPersistedImageEntity();
        $this->assertTrue($entity->getMimeType() === 'jpg');
    }

    public function testFolder()
    {
        $entity = $this->getPersistedImageEntity();
        $this->assertTrue($entity->getFolder() === static::FOLDER);
    }

    public function testFileName()
    {
        $entity = $this->getPersistedImageEntity();
        $this->assertTrue($entity->getFilename() === 'person.jpg');

    }

    public function testType()
    {
        $entity = $this->getPersistedImageEntity();
        $this->assertTrue($entity->getType() === 'image');
    }


    public function testUploaderReturnsFalseIfNoImageWasSet()
    {
        $this->assertFalse($this->uploader->upload(new Image()));
    }

}
