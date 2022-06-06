<?php

namespace Braunstetter\MediaBundle\Tests\Functional;

use App\DatabaseLoader;
use App\MediaBundleKernel;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\ToolsException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;

class AbstractMediaBundleTestCase extends TestCase
{

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    private ContainerInterface $container;

    public Kernel $kernel;
    /**
     * @throws ToolsException
     */
    protected function setUp(): void
    {
        $this->kernel = new MediaBundleKernel($this->provideCustomConfigs());
        $this->kernel->boot();

        $this->container = $this->kernel->getContainer();
        $this->entityManager = $this->getService('doctrine.orm.entity_manager');
        $this->loadDatabaseFixtures();
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

    /**
     * @template T as object
     * @param class-string<T> $type
     * @return T
     */
    protected function getService(string $type): object
    {
        return $this->container->get($type);
    }

}
