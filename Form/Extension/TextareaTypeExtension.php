<?php

namespace JfxNinja\CMSBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TextareaTypeExtension extends AbstractTypeExtension
{


    public function buildView(FormView $view, FormInterface $form, array $options)
    {

        $view->vars['cols'] = 50;
        $view->vars['rows'] = 2;

        if (isset($options['rows']) && $options['rows']) {

            $view->vars['rows'] = $options['rows'];
        }
        if (isset($options['cols']) && $options['cols']) {

            $view->vars['cols'] = $options['cols'];
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(array('rows','cols'));

    }

    public function getDefaultOptions(array $options)
    {
        return array('rows'=>5,'cols'=>50);
    }

    public function getExtendedType()
    {
        return 'textarea';
    }
}