<?php

namespace Braunstetter\MediaBundle\Manager;

use Braunstetter\MediaBundle\Contracts\FileInterface;
use Braunstetter\MediaBundle\Contracts\FileManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Webmozart\Assert\Assert;

class FilesystemManager implements FileManagerInterface
{

    protected LoggerInterface $logger;
    protected Filesystem $filesystem;
    protected SluggerInterface $slugger;
    protected ParameterBagInterface $parameterBag;
    protected string $folder;

    public function __construct(
        LoggerInterface       $logger,
        Filesystem            $filesystem,
        SluggerInterface      $slugger,
        ParameterBagInterface $parameterBag,
    )
    {
        $this->logger = $logger;
        $this->filesystem = $filesystem;
        $this->slugger = $slugger;
        $this->parameterBag = $parameterBag;
    }

    public function upload(FileInterface $fileEntity, bool $uniqFileName = true): bool
    {
        if (!$fileEntity->hasFile()) {
            return false;
        }

        $file = $fileEntity->getFile();

        /** @var UploadedFile $file */
        Assert::isInstanceOf($file, File::class);

        if (null !== $fileEntity->getFullPath() && $this->has($fileEntity->getFullPath())) {
            $this->remove($this->getDirectory() . $fileEntity->getFullPath());
        }

        do {
            $newFilename = $this->createFilename($file, $uniqFileName);
            $path = $this->getFullDirectory() . '/' . $newFilename;
        } while ($this->filesystem->exists($path));

        $fileEntity->setFolder($this->getFolder());
        $fileEntity->setFileName($newFilename);
        $file->move($this->getFullDirectory(), $newFilename);
        $fileEntity->setFile(null);
        return true;
    }

    /**
     * @param string $folder
     * @return FilesystemManager
     */
    public function setFolder(string $folder): FilesystemManager
    {
        $this->folder = $folder;
        return $this;
    }

    public function getFolder(): string
    {
        return $this->folder;
    }

    public function getDirectory(): string
    {
        return $this->parameterBag->get("kernel.project_dir") . '/public';
    }

    public function getFullDirectory(): string
    {
        return $this->getDirectory() . $this->getFolder();
    }

    private function has(string $path): bool
    {
        return $this->filesystem->exists(realpath($this->getDirectory()) . $path);
    }

    public function remove(string $path): bool
    {
        $this->filesystem->exists($path) && $this->filesystem->remove($path);

        return true;
    }

    private function createFilename(UploadedFile $file, bool $uniqFileName = true): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        return $this->slugger->slug($originalFilename) . ($uniqFileName ? '-' . uniqid() : '') . '.' . $file->guessExtension();
    }
}