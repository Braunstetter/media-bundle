<?php

namespace Braunstetter\MediaBundle\Tests\Functional\app\src\Form;

use Braunstetter\MediaBundle\Form\Type\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BlankWrapperForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('image', ImageType::class);
    }

}