<?php

namespace SSone\CMSBundle\Form\FieldType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use SSone\CMSBundle\Form\DataTransformer\MultiLanguageArray;



class MultiLanguageText extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $languageTransformer = new MultiLanguageArray($options['locale']);
        $builder->addModelTransformer($languageTransformer);

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            "locale"=>"en")
        );
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'multiLanguageText';
    }

}



