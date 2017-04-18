<?php

namespace JfxNinja\CMSBundle\Form\Type\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;


class FieldTypeNested extends AbstractType
{

    private $fieldSetupOptions = array();

    public function __construct($fieldSetupOptions){
        $this->fieldSetupOptions = $fieldSetupOptions;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('name', 'text')
            ->add('variableName', 'text')
            ->add('label', 'text')
            ->add('isRepeatable', 'checkbox',array("required"=>false))
            ->add('repeatableGroupLabel', 'text',array("required"=>false))
            ->add('isRequired', 'checkbox',array("required"=>false))
            ->add('requiredText', 'text',array("required"=>false))
            ->add('fieldType','entity',array(
                'class' => 'JfxNinjaCMSBundle:FieldType',
                'property' => 'name',
                'label' => 'Associated Field Type',
                'attr' => array('class' => 'field-type'),
                'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('ft')
                            ->orderBy('ft.name', 'ASC');
                    }
            ))
            ->add('fieldTypeSettings', new FieldTypeSetupOptions($this->fieldSetupOptions), array(
                'label' => '',
                'attr' => array('class' => 'fieldTypeSettings')))
            ->add('sort', 'hidden',array(
                "required"=>false,
                'attr' => array('class' => 'sort')
            ))
        ;


    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'jfxninja\CMSBundle\Entity\Field'
        ));
    }

    public function getName()
    {
        return 'field';
    }

}



