<?php

namespace Braunstetter\MediaBundle;

use Braunstetter\MediaBundle\DependencyInjection\MediaBundleExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MediaBundleBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new MediaBundleExtension();
    }
}