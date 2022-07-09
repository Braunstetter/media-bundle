<?php

namespace Braunstetter\MediaBundle\Tests\Functional\Filesystem;


use Braunstetter\MediaBundle\Tests\Functional\AbstractMediaBundleTestCase;
use Braunstetter\MediaBundle\Tests\TestHelper;

class EventListenerTest extends AbstractMediaBundleTestCase
{

    public function testImageGetsRemovedWhenEntityGetsRemoved()
    {
        $entity = $this->getPersistedImageEntity();

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        $this->assertFalse($this->fileSystem->exists(TestHelper::getPublicDir() . $entity->getFullPath()));
    }

}