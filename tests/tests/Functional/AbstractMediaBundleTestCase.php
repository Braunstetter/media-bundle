<?php

namespace Braunstetter\MediaBundle\Tests\Functional;

use App\DatabaseLoader;
use App\Entity\Media\Image;
use App\MediaBundleKernel;
use Braunstetter\MediaBundle\Contracts\UploaderInterface;
use Braunstetter\MediaBundle\Tests\TestHelper;
use Braunstetter\MediaBundle\Uploader\FilesystemUploader;
use Braunstetter\MediaBundle\Tests\AbstractBaseTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\ToolsException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\PantherTestCaseTrait;
use Symfony\Component\Panther\WebTestAssertionsTrait;

abstract class AbstractMediaBundleTestCase extends AbstractBaseTestCase
{

    use WebTestAssertionsTrait;

    protected EntityManagerInterface $entityManager;
    protected UploaderInterface $uploader;

    public const FIREFOX = 'firefox';
    public const CHROME = 'chrome';
    public const FOLDER = '/tests/images';

    protected ContainerInterface $container;
    public KernelInterface $kernel;

    /**
     * @throws ToolsException
     */
    protected function setUp(): void
    {
        $this->kernel = static::bootKernel($this->provideCustomConfigs());

        $this->container = $this->kernel->getContainer();

        $entityManager = $this->getService('doctrine.orm.entity_manager');
        if ($entityManager instanceof EntityManagerInterface) {
            $this->entityManager = $entityManager;
        }

        $this->loadDatabaseFixtures();

        $uploader = $this->container->get(FilesystemUploader::class);
        if ($uploader instanceof UploaderInterface) {
            $this->uploader = $uploader;
        }

        $this->setFileSystem(new Filesystem());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->takeScreenshotIfTestFailed();

        $this->fileSystem->remove(TestHelper::getTestsDir());
        $this->fileSystem->remove(TestHelper::getPublicDir() . '/media');

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
    }

    protected static function bootKernel(array $options): KernelInterface
    {
        $kernel = static::createKernel($options);
        $kernel->boot();
        return $kernel;
    }

    protected static function createKernel(array $options): KernelInterface
    {
        return new MediaBundleKernel($options);
    }

    /**
     * @throws ToolsException
     */
    protected function loadDatabaseFixtures(): void
    {
        /** @var DatabaseLoader $databaseLoader */
        $databaseLoader = $this->getService(DatabaseLoader::class);
        $databaseLoader->reload();
    }

    /**
     * @return string[]
     */
    protected function provideCustomConfigs(): array
    {
        return [];
    }

    protected function getService(string $type): object|null
    {
        return $this->container->get($type);
    }

    protected function getPersistedImageEntity(): Image
    {
        $entity = TestHelper::createImageEntity('person.jpg');
        $this->uploader->setFolder(self::FOLDER);
        $this->uploader->upload($entity, false);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();


        return $entity;
    }

    protected function initPantherClient(): Client
    {
        return static::createPantherClient(array_replace(static::$defaultOptions, [
            'webServerDir' => TestHelper::getPublicDir(),
            'port' => 9081
        ]));
    }

}
