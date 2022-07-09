<?php

namespace Braunstetter\MediaBundle\Tests\Functional\Ui;

use Braunstetter\MediaBundle\Tests\Functional\AbstractMediaBundleTestCase;
use Braunstetter\MediaBundle\Tests\TestHelper;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Panther\DomCrawler\Field\FileFormField;

class FormTest extends AbstractMediaBundleTestCase
{

    public function test_image_preview()
    {
        $client = $this->initPantherClient();
        $client->request('GET', '/test');

        /** @var FileFormField $fileFormField */
        $fileFormField = $client->getCrawler()->selectButton('Submit')->form()['form[image][0][file]'];
        $fileFormField->upload(TestHelper::getAssetsDir() . '/images/ice.jpg');

        $this->assertSelectorAttributeWillContain('.image-preview > img', 'src', 'yCmivuWqDR3C9UjzBK7HcpVDiAZlXe2OB4IynHnVTZrXMLTkfzG0PS4ag5XJXMSsBbn8DNPhyZLhHMbt5mgsluQlSmZYJhgQgZds6lYGfUGzXcq7v+Id6v');

        $client->takeScreenshot(__dir__ . '/screenshots/image_preview.png');
    }

    public function test_image_preview_with_existing_image()
    {
        $client = new KernelBrowser($this->kernel);
        $client->request('GET', '/test-existing-image');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringStartsWith(
            'http://localhost',
            $client->getCrawler()->filter('.image-preview > img')->attr('src')
        );
    }

}
