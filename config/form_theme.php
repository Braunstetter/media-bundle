<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {

    $containerConfigurator->extension('twig', [
        'form_themes' => ['@MediaBundle/form/image_theme.html.twig'],
    ]);

};

