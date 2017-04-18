<?php

namespace JfxNinja\CMSBundle\Form\FieldType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use JfxNinja\CMSBundle\Form\DataTransformer\MultiLanguageArray;



class MultiLanguageChoice extends AbstractType
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
            "locale"=>"en")
        );
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'multiLanguageChoice';
    }

}



