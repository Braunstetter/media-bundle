<?php

namespace App\Entity;

use App\Entity\Media\Image;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToMany;

#[Entity]
class Page
{

    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue(strategy: 'AUTO')]
    protected ?int $id;

    #[ManyToMany(targetEntity: Image::class)]
    private $image;

    public function __construct()
    {
        $this->image = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return ArrayCollection
     */
    public function getImage(): ArrayCollection
    {
        return $this->image;
    }

    /**
     * @param ArrayCollection $image
     */
    public function setImage(ArrayCollection $image): void
    {
        $this->image = $image;
    }
}