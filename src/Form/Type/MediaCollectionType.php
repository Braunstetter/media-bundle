<?php

namespace Braunstetter\MediaBundle\Form\Type;

use Braunstetter\Helper\Arr;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaCollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            /** @var bool $fillIfEmpty */
            $fillIfEmpty = $form->getConfig()->getOption('fill_if_empty');
            if ($fillIfEmpty && $data->isEmpty()) {
                $form->add(0, $form->getConfig()->getOption('entry_type'), $form->getConfig()->getOption('entry_options'));
            }
        });
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'row_attr' => Arr::attach($view->vars['row_attr'], [
                'data-controller' => 'braunstetter--media-bundle--collection',
                'data-braunstetter--media-bundle--collection-max-items-value' => $options['max_items']
            ]),
            'attr' => Arr::attachClass($view->vars['attr'], 'image-collection'),
            'max_items' => $options['max_items'],
            'include_css' => $options['include_css']
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('by_reference', false);
        $resolver->setDefault('allow_delete', true);

        $resolver->define('max_items')
            ->default(9999)
            ->allowedTypes('int')
            ->info('The maximal items allowed for this collection.');

        $resolver->define('include_css')
            ->default(true)
            ->allowedTypes('bool')
            ->info('Determines whether the supplied css should be injected.');

        $resolver->define('fill_if_empty')
            ->default(true)
            ->allowedTypes('bool')
            ->info('Determines whether the collection should be filled with one empty entry if the collection is empty.');
    }

    public function getParent(): string
    {
        return CollectionType::class;
    }

}