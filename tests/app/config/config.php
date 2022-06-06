<?php

declare(strict_types=1);

use App\Controller\TestController;
use App\DatabaseLoader;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\Amnesia\ValueObject\Symfony\Extension\Doctrine\DBAL;
use Symplify\Amnesia\ValueObject\Symfony\Extension\Doctrine\Mapping;
use Symplify\Amnesia\ValueObject\Symfony\Extension\Doctrine\ORM;
use Symplify\Amnesia\ValueObject\Symfony\Extension\DoctrineExtension;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symplify\Amnesia\Functions\env;

return static function(ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set('env(DB_ENGINE)', 'pdo_sqlite');
    $parameters->set('env(DB_HOST)', 'localhost');
    $parameters->set('env(DB_NAME)', 'orm_media_bundle_test');
    $parameters->set('env(DB_USER)', 'root');
    $parameters->set('env(DB_PASSWD)', '');
    $parameters->set('env(DB_MEMORY)', 'true');
    $parameters->set('kernel.secret', 'for_framework_bundle');
    $parameters->set('locale', 'en');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->set(DatabaseLoader::class);

    $services->set(TestController::class)
        ->tag('controller.service_arguments')
        ->tag('controller.service_subscriber')
        ->autoconfigure(true)
        ->call('setContainer', [service('service_container')])
        ->public();

    $containerConfigurator->extension(DoctrineExtension::NAME, [
        DoctrineExtension::DBAL => [
            DBAL::DBNAME => env('DB_NAME'),
            DBAL::HOST => env('DB_HOST'),
            DBAL::USER => env('DB_USER'),
            DBAL::PASSWORD => env('DB_PASSWD'),
            DBAL::DRIVER => env('DB_ENGINE'),
            DBAL::MEMORY => (bool)env('DB_MEMORY'),
        ],

        DoctrineExtension::ORM => [
            ORM::AUTO_MAPPING => true,
            ORM::MAPPINGS => [
                [
                    Mapping::NAME => 'MediaBundle',
                    Mapping::TYPE => 'attribute',
                    Mapping::PREFIX => 'App\Entity\\',
                    Mapping::DIR => __DIR__ . '/../../../tests/app/src/Entity',
                    Mapping::IS_BUNDLE => false,
                ],
            ],
        ],
    ]);
};
