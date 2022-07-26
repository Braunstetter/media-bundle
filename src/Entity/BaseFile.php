<?php

namespace Braunstetter\MediaBundle\Entity;

use Braunstetter\MediaBundle\Contracts\FileInterface;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\MappedSuperclass;
use ReflectionClass;
use Serializable;
use SplFileInfo;

#[MappedSuperclass]
abstract class BaseFile implements FileInterface, Serializable
{

    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue(strategy: 'AUTO')]
    protected ?int $id;

    /**
     * Custom name of a file.
     * It is generated or set by the user depending
     * on your implementation inside your Uploader
     */
    #[Column(type: 'string', length: 255, nullable: true)]
    protected ?string $filename;

    /**
     * The original name of the file, before it was getting uploaded.
     */
    #[Column(type: 'string', length: 255, nullable: true)]
    protected ?string $originalFilename;

    /**
     * The mimeType of the file.
     */
    #[Column(type: 'string', length: 255, nullable: true)]
    protected ?string $mimeType;

    /**
     * The folder of this file - where the file is located.
     * It should be the real path from inside the public dir of your application.
     */
    #[Column(type: "string", length: 1255, nullable: true)]
    protected ?string $folder;

    /**
     * This property is only used for uploading.
     * Therefore, it can be null (nothing to upload and/or change) or it can be an instance of `SplFileInfo`.
     * This bundle ships with FormTypes which are working with this property to know if a file is ready get processed or not.
     */
    protected ?SplFileInfo $file;

    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    public function getFilename(): ?string
    {
        return $this->filename ?? null;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getOriginalFilename(): ?string
    {
        return $this->originalFilename ?? null;
    }

    public function setOriginalFilename(string $originalFilename): self
    {
        $this->originalFilename = $originalFilename;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType ?? null;
    }

    public function setMimeType(string|null $mimeType): self
    {
        if ($mimeType) {
            $this->mimeType = $mimeType;
        }

        return $this;
    }

    /**
     * This method puts together `folder` and `filename` if set.
     * These properties both has to be set. Otherwise, this method returns null.
     */
    public function getFullPath(): string|null
    {
        return isset($this->folder) && $this->getFilename()
            ? $this->getFolder() . '/' . $this->getFilename()
            : null;
    }

    /**
     * The type of your file.
     * By default, it's the name of the file entity class in lowercase.
     * You should name your media entities with a media-type in mind.
     * E.g. `Image` or `Document`.
     */
    public function getType(): string
    {
        return strtolower((new ReflectionClass($this))->getShortName());
    }

    public function getFolder(): ?string
    {
        return $this->folder;
    }

    public function setFolder(?string $folder): void
    {
        $this->folder = $folder;
    }

    public function getFile(): ?SplFileInfo
    {
        return $this->file ?? null;
    }

    public function setFile(?SplFileInfo $file): void
    {
        $this->file = $file;
    }

    public function hasFile(): bool
    {
        return isset($this->file);
    }

    /** @see \Serializable::serialize() */
    public function serialize(): ?string
    {
        return serialize(array(
            $this->getId(),
            $this->getFullPath(),
            $this->getOriginalFilename(),
            $this->getMimeType(),
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($data)
    {
        return unserialize($data);
    }

    public function __toString(): string
    {
        if ($this->getFullPath()) {
            return $this->getFullPath();
        }

        return (new ReflectionClass($this))->getShortName();
    }

}