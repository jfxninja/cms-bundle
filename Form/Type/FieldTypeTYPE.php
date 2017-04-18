<?php

namespace JfxNinja\CMSBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class fieldTypeTYPE extends AbstractType
{

    private $mode;

    public function __construct($mode){
        $this->mode = $mode;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('variableName', 'text')
            ->add('save', 'submit', array('label'=> 'save ' . $this->mode));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'jfxninja\CMSBundle\Entity\FieldType'
        ));
    }

    public function getName()
    {
        return 'fieldType';
    }

}



