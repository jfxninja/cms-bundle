<?php

namespace JfxNinja\CMSBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LanguageTYPE extends AbstractType
{

    private $mode;

    public function __construct($mode){
        $this->mode = $mode;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('languageCode', 'text')
/*
            ->add('isDefault', 'checkbox',
                array('required'=>false))
*/
            ->add('save', 'submit', array('label'=> 'save ' . $this->mode));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'jfxninja\CMSBundle\Entity\Language'
        ));
    }

    public function getName()
    {
        return 'language';
    }

}



