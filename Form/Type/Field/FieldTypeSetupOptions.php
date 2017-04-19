<?php

namespace JfxNinja\CMSBundle\Form\Type\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use JfxNinja\CMSBundle\Form\DataTransformer\CMSFieldSettingsToArray;


class FieldTypeSetupOptions extends AbstractType
{

    private $inputSetupOptions;

    public function __construct($inputSetupOptions){

        $this->inputSetupOptions = $inputSetupOptions;

    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        foreach($this->inputSetupOptions as $groupKey => $optionsGroup)
        {

            foreach($optionsGroup as $option)
            {

                $variableName = $groupKey . "__" . $option->getVariableName();

                if($option->getInputType() == "choice")
                {
                    $builder->add($variableName, $option->getInputType(), array(
                        'choices'=>$option->getInputTypeVar(),
                        'required'=>false,
                        'label'=>$option->getLabel(),
                        'label_attr' => array('class' => 'group-'.$groupKey.' field-type-setting'),
                        'attr' => array('class' => 'group-'.$groupKey.' field-type-setting ' . $option->getVariableName()),
                        )
                    );

                } elseif($option->getInputType() == "entity") {

                    $builder->add($variableName, "choice", array(
                            'choices'=>$option->getInputTypeVar(),

                            'required'=>false,
                            'label'=>$option->getLabel(),
                            'label_attr' => array('class' => 'group-'.$groupKey.' field-type-setting'),
                            'attr' => array('class' => 'group-'.$groupKey.' field-type-setting ' . $option->getVariableName()),
                        )
                    );

                } else {

                    $builder->add($variableName, $option->getInputType(), array(
                        'required'=>false,
                        'label'=>$option->getLabel(),
                        'label_attr' => array('class' => 'group-'.$groupKey.' field-type-setting'),
                        'attr' => array('class' => 'group-'.$groupKey.' field-type-setting ' . $option->getVariableName()),
                    ));
                }
            }

        }


    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'fieldType' => '' )
        );
    }



    public function getName()
    {
        return 'fieldTypeSettings';
    }

}



