<?php

namespace Braunstetter\MediaBundle\Tests;

use Nyholm\BundleTest\AppKernel;
use Braunstetter\MediaBundle\DependencyInjection\MediaBundleExtension;
use Braunstetter\MediaBundle\MediaBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\HttpKernel\KernelInterface;

class BundleTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return AppKernel::class;
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        /**
         * @var AppKernel $kernel
         */
        $kernel = parent::createKernel($options);
        $kernel->addBundle(MediaBundle::class);
        $kernel->addBundle(TwigBundle::class);

        return $kernel;
    }

    public function testInitBundle(): void
    {
        self::bootKernel();
        $bundle = self::$kernel->getBundle('MediaBundle');
        $this->assertInstanceOf(MediaBundle::class, $bundle);
        $this->assertInstanceOf(MediaBundleExtension::class, $bundle->getContainerExtension());
    }

}