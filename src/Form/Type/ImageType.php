<?php

namespace Braunstetter\MediaBundle\Form\Type;

use Braunstetter\Helper\Arr;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{
    const IMAGE_ENTITY_NAME = 'App\Entity\Media\Image';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('file', FileType::class, $options['file_options']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        if (class_exists(self::IMAGE_ENTITY_NAME)) {
            $resolver->setDefaults(['data_class' => self::IMAGE_ENTITY_NAME]);
        }

        $resolver->define('placeholder_image_path')
            ->allowedTypes('string', 'bool')
            ->allowedValues(static function ($value) {
                return $value === false || is_string($value);
            })
            ->default('bundles/media/images/image-placeholder.jpg')
            ->info('The public path to the placeholder image for this image field. It will be used if no image is uploaded. If you want to disable the placeholder image, set this option to false.');


        $resolver->define('file_options')
            ->allowedTypes('array')
            ->default([])
            ->info('The array of options for the file field.');
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars = array_replace($view->vars, ['row_attr' => Arr::attachClass($view->vars['row_attr'], 'cp--form--single_image')]);

        $view->vars = array_replace($view->vars, [
            'row_attr' => Arr::attach($view->vars['row_attr'], ['data-controller' => 'braunstetter--media-bundle--image-upload'])
        ]);

        $view->vars['placeholder_image_path'] = $options['placeholder_image_path'];

        $view->vars = array_replace($view->vars, [
            'row_attr' => Arr::attach($view->vars['row_attr'], [
                'data-braunstetter--media-bundle--collection-target' => 'field',
            ]),
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'media_image';
    }
}