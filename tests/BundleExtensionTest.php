<?php

namespace Braunstetter\MediaBundle\Tests;

use Braunstetter\MediaBundle\DependencyInjection\MediaBundleExtension;
use Braunstetter\MediaBundle\Entity\EventListeners\FileDeleteListener;
use Braunstetter\MediaBundle\Manager\FilesystemManager;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;

class BundleExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new MediaBundleExtension(),
        ];
    }

    public function test_services_are_getting_loaded()
    {
        $this->load();
        $this->assertContainerBuilderHasService(FilesystemManager::class);
        $this->assertContainerBuilderHasService(FileDeleteListener::class);
    }
}