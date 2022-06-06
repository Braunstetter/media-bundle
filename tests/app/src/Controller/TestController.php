<?php

namespace App\Controller;


use App\Entity\Page;
use Braunstetter\MediaBundle\Form\Type\ImageCollectionItemType;
use Braunstetter\MediaBundle\Form\Type\ImageCollectionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class TestController extends AbstractController
{

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function test(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, Environment $environment, Request $request): Response
    {
        $form = $formFactory->createBuilder(FormType::class, new Page());
        $form ->add('image', ImageCollectionType::class, [
            'entry_type' => ImageCollectionItemType::class,
            'entry_options' => [
                'required' => false,
                'label' => false,
            ],
            'allow_add' => true,
            'allow_delete' => true
        ]);
        $form = $form->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dump('hoho');
        }

        return new Response(
            $environment->render('form/page_index.html.twig', [
                'form' => $form->createView()
            ])
        );
    }
}