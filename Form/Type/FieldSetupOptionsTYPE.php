<?php

namespace JfxNinja\CMSBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class fieldSetupOptionsTYPE extends AbstractType
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
                    'entity' => 'Entity',
                    'checkbox'=>'Checkbox')
            ))

            ->add('inputTypeVar', 'textarea', array('required'=>false))
            ->add('fieldType','entity',array(
                'class' => 'JfxNinjaCMSBundle:FieldType',
                'property' => 'name',
                'label' => 'Associated Field Type'
            ))
            ->add('save', 'submit', array('label'=> 'save ' . $this->mode));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'jfxninja\CMSBundle\Entity\FieldSetupOptions'
        ));
    }

    public function getName()
    {
        return 'fieldSetupOptions';
    }

}



