<?php

namespace JfxNinja\CMSBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class fieldTYPE extends AbstractType
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
            ->add('label', 'text')
            ->add('ContentType','entity',array(
                'class' => 'JfxNinjaCMSBundle:ContentType',
                'property' => 'name',
                'label' => 'Associated Content Type'
            ))
            ->add('fieldType','entity',array(
                'class' => 'JfxNinjaCMSBundle:FieldType',
                'property' => 'name',
                'label' => 'Associated Field Type',
                'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('ft')
                            ->orderBy('ft.name', 'ASC');
                    }
            ))
            ->add('save', 'submit', array('label'=> 'save ' . $this->mode));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'JfxNinja\CMSBundle\Entity\Field'
        ));
    }

    public function getName()
    {
        return 'field';
    }

}



