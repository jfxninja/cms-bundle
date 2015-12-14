<?php

namespace SSone\CMSBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


use SSone\CMSBundle\Form\Type\Block\BlockTYPE;

class ContentTypeAttributesTYPE extends AbstractType
{

    private $mode;
    private $locale;
    private $fieldsRepository;
    private $cs;

    public function __construct($mode,$fieldsRepository,$cs,$locale){
        $this->mode = $mode;
        $this->locale = $locale;
        $this->fieldsRepository = $fieldsRepository;
        $this->cs = $cs;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));

    }


    public function onPreSetData(FormEvent $event){

        $form = $event->getForm();

        $form->add('name', 'text');
        $form->add('slug', 'multiLanguageText', array("locale"=>$this->locale));
        $form
            ->add('blocks', 'collection', array(
            'type' => new BlockTYPE($this->locale,$this->fieldsRepository,$this->cs)
        ));


        $form->add('save', 'submit', array('label'=> 'save ' . $this->mode));
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SSone\CMSBundle\Entity\ContentType'
        ));
    }


    public function getName()
    {
        return 'contentType';
    }

}



