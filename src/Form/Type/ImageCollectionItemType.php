<?php

namespace Braunstetter\MediaBundle\Form\Type;

use Braunstetter\Helper\Arr;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageCollectionItemType extends AbstractType
{
    const IMAGE_ENTITY_NAME = 'App\Entity\Media\Image';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', FileType::class, $options['file_options']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        if (class_exists(self::IMAGE_ENTITY_NAME)) {
            $resolver->setDefaults(['data_class' => self::IMAGE_ENTITY_NAME]);
        }

        $resolver->define('placeholder_image_path')
            ->allowedTypes('string')
            ->default('bundles/media/images/image-placeholder.jpg')
            ->info('The public path to the placeholder image to be shown for this image field.');

        $resolver->define('file_options')
            ->allowedTypes('array')
            ->default([])
            ->info('The array of options passed to the file field.');
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, ['row_attr' => Arr::attachClassToAttr($view->vars['row_attr'], 'cp--form--single_image')]);

        $view->vars = array_replace($view->vars, [
            'row_attr' => Arr::attachToAttr($view->vars['row_attr'], ['data-controller' => 'braunstetter--media-bundle--image-upload'])
        ]);

        $view->vars['placeholder_image_path'] = $options['placeholder_image_path'];

        $view->vars = array_replace($view->vars, [
            'row_attr' => Arr::attachToAttr($view->vars['row_attr'], [
                'data-braunstetter--media-bundle--form-collection-target' => 'field',
            ]),
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'image_collection_item';
    }
}