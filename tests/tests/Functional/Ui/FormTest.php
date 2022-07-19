<?php

namespace Braunstetter\MediaBundle\Tests\Functional\Ui;

use Braunstetter\MediaBundle\Tests\Functional\AbstractMediaBundleTestCase;
use Braunstetter\MediaBundle\Tests\TestHelper;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Panther\DomCrawler\Field\FileFormField;
use Symfony\Component\VarDumper\VarDumper;

class FormTest extends AbstractMediaBundleTestCase
{

    public function test_image_preview()
    {
        $client = $this->initPantherClient();
        $client->request('GET', '/test');

        $client->takeScreenshot(__dir__ . '/screenshots/empty_form.png');

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
            $client->getCrawler()->filter('.image-preview > img')->attr('src') ?? ''
        );
    }

    public function test_collection_item_is_added()
    {
        $client = $this->initPantherClient();
        $client->request('GET', '/test');
        $crawler = $client->getCrawler();

        $crawler->filter('div.image-collection-actions > button')->click();
        $this->assertSame(2, $crawler->filter('.image-collection > div')->count());

        $crawler->filter('div.image-collection-actions > button')->click();
        $this->assertSame(3, $crawler->filter('.image-collection > div')->count());

        $client->takeScreenshot(__dir__ . '/screenshots/collection_item_gets_added.png');
    }

    public function test_max_items_option_hides_add_button()
    {
        $client = $this->initPantherClient();

        $client->request('GET', '/test-two-existing-images?options=' . json_encode(['max_items' => 3]));

        $client->getCrawler()->filter('div.image-collection-actions > button')->click();
        $this->assertSame(3, $this->getImageCount($client->getCrawler()));
        $this->assertSame(1, $this->getAddButton($client, false)->count());

        $client->takeScreenshot(__dir__ . '/screenshots/max_items_option_hides_add_button.png');
    }

    public function test_max_items_option_hides_add_button_on_initial_pageload()
    {
        $client = new KernelBrowser($this->kernel);
        $client->request('GET', '/test-two-existing-images?options=' . json_encode(['max_items' => 2]));

        $this->assertSame(1, $this->getAddButton($client, false)->count());
    }

    public function test_include_css_option_default_include_css()
    {
        $client = new KernelBrowser($this->kernel);
        $client->request('GET', '/test');

        $this->assertSame(1, $client->getCrawler()->filterXPath('//link[contains(@href, "/bundles/media/build/bundle.css")]')->count());
    }

    public function test_include_css_option_do_not_include_css_when_set_to_false()
    {
        $client = new KernelBrowser($this->kernel);
        $client->request('GET', '/test?options=' . json_encode(['include_css' => false]));
        $this->assertSame(0, $client->getCrawler()->filterXPath('//link[contains(@href, "/bundles/media/build/bundle.css")]')->count());
    }

    private function getAddButton(AbstractBrowser $client, bool $enabled = true): Crawler
    {
        $basePath = 'div.image-collection-actions > button';
        return $client->getCrawler()->filter($enabled ? $basePath : $basePath . '.hidden');
    }

    private function getImageCount(Crawler $crawler): int
    {
        return $crawler->filter('.image-collection > div')->count();
    }

}
