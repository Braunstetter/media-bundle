<?php

namespace App;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;

final class DatabaseLoader
{
    public function __construct(private EntityManagerInterface $entityManager, Connection $connection) {
        // @see https://stackoverflow.com/a/35222045/1348344
        $configuration = $connection->getConfiguration();
        $configuration->setSQLLogger();
    }

    /**
     * @throws ToolsException
     */
    public function reload(): void
    {
        $classMetadataFactory = $this->entityManager->getMetadataFactory();

        $classesMetadatas = $classMetadataFactory->getAllMetadata();

        $entityClasses = [];
        foreach ($classesMetadatas as $classMetadata) {
            $entityClasses[] = $classMetadata->getName();
        }

        $this->reloadEntityClasses($entityClasses);
    }

    /**
     * @param string[] $entityClasses
     * @throws ToolsException
     */
    public function reloadEntityClasses(array $entityClasses): void
    {
        $schema = [];
        foreach ($entityClasses as $entityClass) {
            $schema[] = $this->entityManager->getClassMetadata($entityClass);
        }

        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropSchema($schema);
        $schemaTool->createSchema($schema);
    }
}