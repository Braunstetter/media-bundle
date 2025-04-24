<?php

namespace Braunstetter\MediaBundle\Entity\EventListeners;

use Braunstetter\MediaBundle\Entity\BaseFile as File;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class FileDeleteListener
{
    private Filesystem $filesystem;
    private ParameterBagInterface $parameterBag;

    public function __construct(Filesystem $filesystem, ParameterBagInterface $parameterBag)
    {
        $this->filesystem = $filesystem;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @throws Exception
     */
    public function postRemove(PostRemoveEventArgs $args): void
    {
        /** @var File $object */
        $object = $args->getObject();

        if ($object instanceof File && $object->getFullPath()) {
            $path = $this->getFileSystemFullPath($object);
            $this->filesystem->exists($path) && $this->filesystem->remove($path);
        }
    }

    public function getDirectory(): string
    {
        return $this->parameterBag->get("kernel.project_dir") . '/public';
    }

    public function getFileSystemFullPath(File $file): string
    {
        return $this->getDirectory() . $file->getFullPath();
    }

}