<?php

namespace Braunstetter\MediaBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractBaseTestCase extends TestCase
{

    protected Filesystem $fileSystem;

    public function setFileSystem(Filesystem $fileSystem): void
    {
        $this->fileSystem = $fileSystem;
    }
}