<?php

namespace Braunstetter\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

class ImageCollectionItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, $options['file_options'])
            ;

    }

    public function getParent(): string
    {
        return ImageType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'image_collection_item';
    }


}