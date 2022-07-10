<?php

namespace Braunstetter\MediaBundle\Uploader;

use Braunstetter\MediaBundle\Contracts\FileInterface;
use Braunstetter\MediaBundle\Contracts\UploaderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Webmozart\Assert\Assert;

class FilesystemUploader implements UploaderInterface
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

        $newFilename = $this->createNewFilename($file, $uniqFileName);

        $fileEntity->setFolder($this->getFolder());

        $fileEntity->setFileName($newFilename);
        $fileEntity->setOriginalFilename($file->getFilename());
        $fileEntity->setMimeType($file->guessExtension());
        $file->move($this->getFullDirectory(), $newFilename);
        $fileEntity->setFile(null);
        return true;
    }

    /**
     * @param string $folder
     * @return FilesystemUploader
     */
    public function setFolder(string $folder): FilesystemUploader
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

        if (!is_string($originalFilename)) {
            return $file->getClientOriginalName();
        }
        
        return $this->slugger->slug($originalFilename) . ($uniqFileName ? '-' . uniqid() : '') . '.' . $file->guessExtension();
    }

    private function createNewFilename(UploadedFile $file, bool $uniqFileName): string
    {
        do {
            $newFilename = $this->createFilename($file, $uniqFileName);
            $path = $this->getFullDirectory() . '/' . $newFilename;
        } while ($this->filesystem->exists($path));

        return $newFilename;
    }
}