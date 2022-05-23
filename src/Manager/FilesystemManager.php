<?php

namespace Braunstetter\MediaBundle\Manager;

use Braunstetter\MediaBundle\Contracts\FileInterface;
use Braunstetter\MediaBundle\Contracts\FileManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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

    public function upload(FileInterface $fileEntity): void
    {
        if (!$fileEntity->hasFile()) {
            return;
        }

        $file = $fileEntity->getFile();

        /** @var UploadedFile $file */
        Assert::isInstanceOf($file, File::class);

        if (null !== $fileEntity->getFullPath() && $this->has($fileEntity->getFullPath())) {
            $this->remove($fileEntity->getFullPath());
        }

        do {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

            $path = $this->getFullDirectory() . $newFilename;

        } while ($this->filesystem->exists($path));

        $fileEntity->setFolder($this->getFolder());
        $fileEntity->setFileName($newFilename);

        try {
            $file->move(
                $this->getFullDirectory(),
                $newFilename
            );
        } catch (FileException) {
            $this->logger->alert(sprintf('Uploaded file "%s" could not be moved to the new location "%s"',
                    $newFilename,
                    $this->getFullDirectory() . '/' . $newFilename)
            );
        }

        $fileEntity->setFile(null);

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

    /**
     * @return string
     */
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
        return $this->getDirectory() . $this->getFolder() . '/';
    }

    private function has(string $path): bool
    {
        return $this->filesystem->exists($path);
    }

    public function remove(string $path): bool
    {
        if ($this->filesystem->exists($path)) {
            try {
                $this->filesystem->remove($path);
            } catch (Exception) {
                return false;
            }
        }

        return true;
    }

}