<?php

namespace JfxNinja\CMSBundle\Form\FieldType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use JfxNinja\CMSBundle\Form\DataTransformer\CMSChoiceFieldTransformer;



class CMSChoiceField extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $transformer = new CMSChoiceFieldTransformer($options);
        $builder->addModelTransformer($transformer);

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
        return 'CMSChoice';
    }

}



