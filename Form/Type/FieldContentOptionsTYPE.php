<?php

namespace SSone\CMSBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class fieldContentOptionsTYPE extends AbstractType
{

    private $mode;
    private $entityManager;

    public function __construct($mode, EntityManager $entityManager){
        $this->mode = $mode;
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('name', 'text')
            ->add('variableName', 'text')
            ->add('label', 'text')
            ->add('inputType', 'choice', array(
                'choices' => array(
                    'text'=>'Text Input',
                    'textarea' => 'Text Area',
                    'choice' => 'Dropdown',
                    'radio' => 'Radio Buttons',
                    'checkbox'=>'Checkbox')
            ))
            ->add('inputTypeVar', 'textarea', array('required'=>false))
            ->add('fieldType','entity',array(
                'class' => 'SSoneCMSBundle:FieldType',
                'property' => 'name',
                'label' => 'Associated Field Type'
            ))
            ->add('save', 'submit', array('label'=> 'save ' . $this->mode));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SSone\CMSBundle\Entity\FieldContentOptions'
        ));
    }

    public function getName()
    {
        return 'fieldSetupOptions';
    }

}



