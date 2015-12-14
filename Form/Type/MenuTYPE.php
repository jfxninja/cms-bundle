<?php

namespace SSone\CMSBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MenuTYPE extends AbstractType
{

    private $mode;

    public function __construct($mode){
        $this->mode = $mode;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('domain','entity',array(
                'class' => 'SSoneCMSBundle:Domain',
                'property' => 'name',
                'label' => 'Domain'
            ))
            ->add('sort', 'text')

            ->add('menuTemplate', 'text', array('required'=>false))
            ->add('file_menuTemplate', 'file',
                array(
                    'required'=>false,
                    'mapped'=>false,
                    'label'=>"or upload"))
            ->add('grandChildrenRelativePosition', 'choice',
                array('choices'=>array(
                    "inline"=>"Inline",
                    "separate"=>"Separate")))
            ->add('drawAllGrandChildren', 'choice', array('choices'=>array(1=>"Yes",0=>"No")))
            ->add('menuTemplatePosition', 'choice',
                array('choices'=>array(
                    "A"=>"Position A",
                    "B"=>"Position B",
                    "C"=>"Position C",
                    "D"=>"Position D",
                    "E"=>"Position E",
                    "F"=>"Position F",
                    "G"=>"Position G",
                    "H"=>"Position H")))
            ->add('grandChildrenTemplatePosition', 'choice',
                array('choices'=>array(
                    "A"=>"Position A",
                    "B"=>"Position B",
                    "C"=>"Position C",
                    "D"=>"Position D",
                    "E"=>"Position E",
                    "F"=>"Position F",
                    "G"=>"Position G",
                    "H"=>"Position H")))
            ->add('save', 'submit', array('label'=> 'save ' . $this->mode));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SSone\CMSBundle\Entity\Menu'
        ));
    }

    public function getName()
    {
        return 'menu';
    }

}



