<?php

namespace Braunstetter\MediaBundle\Tests\Functional;

use App\Entity\Media\Image;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class MediaBundleTest extends AbstractMediaBundleTestCase
{

    private ObjectRepository $objectRepository;


    protected function setUp(): void
    {
        parent::setUp();

        $this->objectRepository = $this->entityManager->getRepository(Image::class);
    }

    public function test_the_test()
    {
        $client = new KernelBrowser($this->kernel);
        $client->request('GET', '/test');

        dump($client->getResponse()->getContent());
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
}
