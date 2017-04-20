<?php

namespace JfxNinja\CMSBundle\Form\Type\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;


class FieldTypeNested extends AbstractType
{

    private $inputSetupOptions;
    private $inputTypes;

    public function __construct($inputTypes,$inputSetupOptions){
        $this->inputSetupOptions = $inputSetupOptions;
        $this->inputTypes = $inputTypes;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        foreach($this->inputTypes as $type)
        {
            $inputTypeChoices[$type->getVariableName()] = $type->getName();
        }


        $builder
            ->add('name', 'text')
            ->add('variableName', 'text')
            ->add('label', 'text')
            ->add('isRepeatable', 'checkbox',array("required"=>false))
            ->add('repeatableGroupLabel', 'text',array("required"=>false))
            ->add('isRequired', 'checkbox',array("required"=>false))
            ->add('requiredText', 'text',array("required"=>false))
            ->add('type', 'choice',array(
                    'required' => true,
                    'expanded' => false,
                    'multiple' => false,
                    'attr' => array('class' => 'field-type'),
                    'choices' => $inputTypeChoices
                )
            )
            ->add('fieldTypeSettings', new FieldTypeSetupOptions($this->inputSetupOptions), array(
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
            'data_class' => 'JfxNinja\CMSBundle\Entity\Field'
        ));
    }

    public function getName()
    {
        return 'field';
    }

}



