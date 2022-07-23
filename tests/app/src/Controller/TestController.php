<?php

namespace App\Controller;


use App\Entity\Page;
use Braunstetter\MediaBundle\Form\Type\ImageType;
use Braunstetter\MediaBundle\Form\Type\MediaCollectionType;
use Braunstetter\MediaBundle\Tests\Functional\AbstractMediaBundleTestCase;
use Braunstetter\MediaBundle\Tests\TestHelper;
use Braunstetter\MediaBundle\Uploader\FilesystemUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class TestController extends AbstractController
{
    const BASE_FORM_OPTIONS = [
        'entry_type' => ImageType::class,
        'entry_options' => [
            'required' => false,
            'label' => false,
        ],
        'allow_add' => true,
        'allow_delete' => true
    ];

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function test(FormFactoryInterface $formFactory, Environment $environment, Request $request, FilesystemUploader $filesystemManager): Response
    {
        $form = $this->getForm($formFactory, null, $this->getOptions($request));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->uploadImage($filesystemManager, $form);
        }

        return new Response(
            $environment->render('form/page_index.html.twig', ['form' => $form->createView()])
        );
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function testWithExistingImage(FormFactoryInterface $formFactory, Environment $environment, FilesystemUploader $filesystemManager, Request $request): Response
    {
        $page = new Page();
        $page->addImage(TestHelper::createImageEntity('person.jpg'));
        $form = $this->getForm($formFactory, $page, $this->getOptions($request));
        $this->uploadImage($filesystemManager, $form);

        return new Response(
            $environment->render('form/page_index.html.twig', ['form' => $form->createView()])
        );
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function testWithTwoExistingImages(FormFactoryInterface $formFactory, Environment $environment, FilesystemUploader $filesystemManager, Request $request): Response
    {
        $page = new Page();
        $page->addImage(TestHelper::createImageEntity('person.jpg'));
        $page->addImage(TestHelper::createImageEntity('ice.jpg'));
        $form = $this->getForm($formFactory, $page, $this->getOptions($request));
        $this->uploadImage($filesystemManager, $form);

        return new Response(
            $environment->render('form/page_index.html.twig', ['form' => $form->createView()])
        );
    }

    private function getForm(FormFactoryInterface $formFactory, Page|null $entity = null, array|null $options = []): FormInterface
    {
        $form = $formFactory->createBuilder(FormType::class, $entity ?? new Page());

        $options = $options
            ? array_replace(static::BASE_FORM_OPTIONS, $options)
            : static::BASE_FORM_OPTIONS;

        $form->add('image', MediaCollectionType::class, $options)
            ->add('submit', SubmitType::class);


        return $form->getForm();
    }

    private function uploadImage(FilesystemUploader $filesystemManager, FormInterface $form): void
    {
        $filesystemManager->setFolder(AbstractMediaBundleTestCase::FOLDER);

        foreach ($form->get('image')->getData() as $file) {
            $filesystemManager->upload($file);
        }
    }

    private function getOptions(Request $request): array
    {
        return json_decode($request->query->get('options'), true) ?? [];
    }
}