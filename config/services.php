<?php

declare(strict_types=1);

use Braunstetter\MediaBundle\Entity\EventListeners\FileDeleteListener;
use Braunstetter\MediaBundle\Manager\FilesystemManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {

    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Braunstetter\MediaBundle\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/MediaBundleBundle.php',]);

    $services->set(FilesystemManager::class)
        ->arg('$logger', service(LoggerInterface::class))
        ->arg('$filesystem', service(Symfony\Component\Filesystem\Filesystem::class))
        ->arg('$slugger', service(SluggerInterface::class))
        ->arg('$parameterBag', service(ParameterBagInterface::class));

    $services->set(FileDeleteListener::class)
        ->tag('doctrine.event_listener', ['event' => 'postRemove']);
};
