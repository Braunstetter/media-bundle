<?php

namespace Braunstetter\MediaBundle\Entity;

use Braunstetter\MediaBundle\Contracts\FileInterface;
use Doctrine\ORM\Mapping as ORM;
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

    #[Column(type: 'string', length: 255, nullable: true)]
    protected ?string $filename;

    #[Column(type: 'string', length: 255, nullable: true)]
    protected ?string $originalFilename;

    #[Column(type: 'string', length: 255, nullable: true)]
    protected ?string $mimeType;

    #[Column(type: "string", length: 1255, nullable: true)]
    protected ?string $folder;

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

    public function getFullPath(): string|null
    {
        return isset($this->folder) && $this->getFilename()
            ? $this->getFolder() . '/' . $this->getFilename()
            : null;
    }

    public function getType(): string
    {
        return strtolower((new ReflectionClass($this))->getShortName());
    }

    /**
     * @return string|null
     */
    public function getFolder(): ?string
    {
        return $this->folder;
    }

    /**
     * @param string|null $folder
     */
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