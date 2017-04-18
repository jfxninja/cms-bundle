<?php

namespace JfxNinja\CMSBundle\Form\Type\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use JfxNinja\CMSBundle\Form\DataTransformer\CMSFieldSettingsToArray;


class FieldTypeSetupOptions extends AbstractType
{

    private $fieldSetupOptions;

    public function __construct($fieldSetupOptions){

        $this->fieldSetupOptions = $fieldSetupOptions;

    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        foreach($this->fieldSetupOptions as $groupKey => $optionGroup)
        {

            foreach($optionGroup['options'] as $option)
            {
                if($option['inputType'] == "choice")
                {
                    $builder->add($option['variableName'], $option['inputType'], array(
                        'choices'=>$option['inputTypeVar'],
                        'required'=>false,
                        'label'=>$option['label'],
                        'label_attr' => array('class' => 'group-'.$groupKey.' field-type-setting'),
                        'attr' => array('class' => 'group-'.$groupKey.' field-type-setting ' . $option['variableName']),
                        )
                    );

                } elseif($option['inputType'] == "entity") {

                    $builder->add($option['variableName'], "choice", array(
                            'choices'=>$option['inputTypeVar'],

                            'required'=>false,
                            'label'=>$option['label'],
                            'label_attr' => array('class' => 'group-'.$groupKey.' field-type-setting'),
                            'attr' => array('class' => 'group-'.$groupKey.' field-type-setting ' . $option['variableName']),
                        )
                    );

                } else {

                    $builder->add($option['variableName'], $option['inputType'], array(
                        'required'=>false,
                        'label'=>$option['label'],
                        'label_attr' => array('class' => 'group-'.$groupKey.' field-type-setting'),
                        'attr' => array('class' => 'group-'.$groupKey.' field-type-setting ' . $option['variableName']),
                    ));
                }
            }

        }

        $builder->addViewTransformer(new CMSFieldSettingsToArray($this->fieldSetupOptions));

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



