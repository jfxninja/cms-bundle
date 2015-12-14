<?php

namespace SSone\CMSBundle\Form\FieldType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use SSone\CMSBundle\Form\DataTransformer\MultiLanguageArray;



class MultiLanguageTextarea extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $languageTransformer = new MultiLanguageArray($options['locale']);
        $builder->addModelTransformer($languageTransformer);

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        //todo:jw retreive default locale
        $resolver->setDefaults(array(
            "locale"=>"en",
            "rows"=>"",
            "cols"=>"",)
        );
    }

    public function getParent()
    {
        return 'textarea';
    }

    public function getName()
    {
        return 'multiLanguageTextarea';
    }

}



