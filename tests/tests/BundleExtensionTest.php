<?php

namespace Braunstetter\MediaBundle\Tests;

use Braunstetter\MediaBundle\DependencyInjection\MediaBundleExtension;
use Braunstetter\MediaBundle\Entity\EventListeners\FileDeleteListener;
use Braunstetter\MediaBundle\Uploader\FilesystemUploader;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Symfony\Component\DependencyInjection\Extension\Extension;

class BundleExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setParameter('kernel.project_dir', '/');
        $this->setParameter('kernel.bundles_metadata', []);
    }

    protected function getContainerExtensions(): array
    {
        return [
            new MediaBundleExtension(),
            new TwigExtension(),
        ];
    }

    public function test_services_are_getting_loaded()
    {
        $this->load();
        $this->assertContainerBuilderHasService(FilesystemUploader::class);
        $this->assertContainerBuilderHasService(FileDeleteListener::class);

        /** @var TwigExtension $twigExtension */
        $twigExtension = $this->container->getExtension('twig');
        $this->assertInstanceOf(TwigExtension::class, $twigExtension);
        $this->assertTrue($this->formThemeExists($twigExtension, 'form_div_layout.html.twig'));
    }

    private function getConfig(Extension $extension, string $name)
    {
        $result = null;
        foreach ($extension->getProcessedConfigs() as $config) {
            if (array_key_exists($name, $config)) {
                $result = $config[$name];
            }
        }

        return $result;
    }

    private function formThemeExists(TwigExtension $twigExtension, string $name): bool
    {
        return in_array($name, $this->getConfig($twigExtension, 'form_themes'));
    }
}