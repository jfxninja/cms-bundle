<?php

namespace JfxNinja\CMSBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityRepository;

use JfxNinja\CMSBundle\Form\Type\Block\BlockTYPE;

class ContentTYPEfrontend extends AbstractType
{

    private $buttonText;
    private $locale;
    private $cs;
    private $fieldsRepository;
    private $CMSFormService;

    public function __construct($buttonText,$fieldsRepository,$cs,$CMSFormService,$locale){
        $this->buttonText = $buttonText;
        $this->locale = $locale;
        $this->cs = $cs;
        $this->fieldsRepository = $fieldsRepository;
        $this->CMSFormService = $CMSFormService;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));

    }


    public function onPreSetData(FormEvent $event){

        $form = $event->getForm();

        $form
            ->add('blocks', 'collection', array(
            'type' => new BlockTYPE($this->locale, $this->fieldsRepository, $this->cs,$this->CMSFormService),
        ));


        $form->add('save', 'submit', array('label'=> $this->buttonText));
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'jfxninja\CMSBundle\Entity\Content'
        ));
    }


    public function getName()
    {
        return 'content';
    }

}



