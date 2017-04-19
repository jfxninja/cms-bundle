<?php

namespace JfxNinja\CMSBundle\Form\Type\Block;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class BlockFieldBuilder extends AbstractType
{
    private $field;
    private $options;
    private $fieldsRepository;
    private $cmsInputTypeService;
    private $cs;
    private $locale;

    public function __construct($field, $cmsInputTypeService, $fieldsRepository, $cs, $locale){

        $this->field = $field;
        $this->cmsInputTypeService = $cmsInputTypeService;
        $this->fieldsRepository = $fieldsRepository;
        $this->cs = $cs;
        $this->locale = $locale;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $this->options = $options;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));

    }

    public function onPreSetData(FormEvent $event){

        $form = $event->getForm();
        $field = $this->field;

        $form
            ->add("fieldContent", new BlockFieldInputsBuilder($field,$this->fieldsRepository,$this->cs,$this->locale), array(
                'label' => false,
            ))
            ->add('sort', 'hidden',array(
                "required"=>false,
                'attr' => array('class' => 'sort')
            ));


    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'JfxNinja\CMSBundle\Entity\BlockField',
            'fieldType' => '' )
        );
    }


    public function getName()
    {
        return 'BlockFieldBuilder';
    }

    private function convertChoiceOptionsStringToArray($stringOptions)
    {
        $options = array();

        $stringOptions = rtrim($stringOptions, ";");


        foreach(explode(";",$stringOptions) as $stringOption)
        {

            $arrayOption = explode("=",$stringOption);

            $options[trim($arrayOption[1])] = trim($arrayOption[0]);

        }
        return $options;
    }

}



