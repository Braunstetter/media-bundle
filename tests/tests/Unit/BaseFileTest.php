<?php

namespace Braunstetter\MediaBundle\Tests\tests\Unit;

use App\Entity\Media\Image;
use PHPUnit\Framework\TestCase;

class BaseFileTest extends TestCase
{
    private Image $entity;

    protected function setUp(): void
    {
        $this->entity = new Image();
    }

    public function test_id()
    {
        $this->assertNull($this->entity->getId());
    }

    public function test_original_filename()
    {
        $this->assertNull($this->entity->getOriginalFilename());
        $this->entity->setOriginalFilename($filename = 'my_file_name.jpeg');
        $this->assertSame($this->entity->getOriginalFilename(), $filename);
    }

    public function test_mime_type()
    {
        $this->assertNull($this->entity->getMimeType());
        $this->entity->setMimeType($mimeType = 'image/png');
        $this->assertSame($this->entity->getMimeType(), $mimeType);
    }

    public function test_type()
    {
        $this->assertSame($this->entity->getType(), 'image');
    }

    public function test_serialize()
    {
        $this->assertSame(unserialize($this->entity->serialize()), [null, null, null, null]);
    }

    public function test_unserialize()
    {
        $data = $this->entity->serialize();
        $this->assertSame($this->entity->unserialize($data), [null, null, null, null]);
    }

    public function test_to_string()
    {
        $this->assertSame($this->entity->__toString(), 'Image');

        $this->entity->setFolder('/my_folder');
        $this->entity->setFilename('my_image.jpeg');

        $this->assertSame($this->entity->__toString(), '/my_folder/my_image.jpeg');
    }


}