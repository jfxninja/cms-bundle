<?php

namespace JfxNinja\CMSBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DomainTYPE extends AbstractType
{

    private $mode;
    private $locale;

    public function __construct($mode,$locale){
        $this->locale = $locale;
        $this->mode = $mode;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('domain', 'text')
            ->add('metaDescription', 'multiLanguageTextarea', array("locale"=>$this->locale,'required'=>false))
            ->add('domainHTMLTemplate', 'text', array('required'=>false))
            ->add('themeBundleName', 'text', array('required'=>false))
            ->add('file_domainHTMLTemplate', 'file',
                array(
                    'required'=>false,
                    'mapped'=>false,
                    'label'=>"or upload"))

            ->add('save', 'submit', array('label'=> 'save ' . $this->mode));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'JfxNinja\CMSBundle\Entity\Domain'
        ));
    }

    public function getName()
    {
        return 'domain';
    }

}



