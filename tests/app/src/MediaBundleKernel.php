<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;

class MediaBundleKernel extends Kernel
{

    use MicroKernelTrait;

    /**
     * @param string[] $configs
     */
    public function __construct(private array $configs = [])
    {
        parent::__construct('test', true);
    }

    private function configureContainer(ContainerConfigurator $container): void
    {
        $container->import(__DIR__ . '/../config/config.php');


        $container->extension('twig', [
            'paths' => [
                $this->getProjectDir() . '/templates' => '__main__',
                parent::getProjectDir() . '/src/Resources/views' => 'Media'
            ]
        ]);
    }

    /**
     * Gets the application root dir (path of the project's composer file).
     */
    public function getProjectDir(): string
    {
        $defaultDir = parent::getProjectDir();

        return $defaultDir . '/tests/app';
    }

}