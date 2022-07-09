<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function(ContainerConfigurator $containerConfigurator): void {

    $containerConfigurator->extension('liip_imagine', [
        'filter_sets' => ['form_preview' => [
            'quality' => 50,
            'filters' => [
                'thumbnail' =>[
                    'size' => [600, 600],
                    'mode' => "inset"
                ]
            ]
        ]],
    ]);

};
