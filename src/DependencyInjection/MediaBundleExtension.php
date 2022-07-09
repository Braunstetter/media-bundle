<?php

namespace Braunstetter\MediaBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class MediaBundleExtension extends Extension implements PrependExtensionInterface
{

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $phpFileLoader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $phpFileLoader->load('services.php');
    }

    /**
     * @throws \Exception
     */
    public function prepend(ContainerBuilder $container)
    {
        $phpFileLoader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../../config'));

        if ($container->hasExtension('twig')) {
            $phpFileLoader->load('form_theme.php');
        }

        if ($container->hasExtension('liip_imagine')) {
            $phpFileLoader->load('filter_sets.php');
        }
    }
}