<?php

namespace JfxNinja\CMSBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class CMSFormTYPE extends AbstractType
{

    private $mode;
    private $locale;

    public function __construct($mode,$locale){
        $this->mode = $mode;
        $this->locale = $locale;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('formTitle', 'multiLanguageText', array("locale"=>$this->locale))
            ->add('contentType','entity',array(
                'class' => 'JfxNinjaCMSBundle:ContentType',
                'property' => 'name',
                'label' => 'Content Type',
                'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->add('where', 'c.hideFromMenus != 1')
                            ->orderBy('c.name', 'ASC');
                    }
            ))
            ->add('buttonText', 'text', array('required'=>false))
            ->add('successURL', 'multiLanguageText', array("locale"=>$this->locale))

            ->add('template', 'text', array('required'=>false))

            ->add('file_template', 'file',
                array(
                    'required'=>false,
                    'mapped'=>false,
                    'label'=>"or upload"))


            ->add('sendAdminEmailOnSubmit', 'checkbox',
                array("required"=>false))
            ->add('adminEmailToAddress', 'text', array("required"=>false))
            ->add('adminEmailFromAddress', 'text', array("required"=>false))

            ->add('adminEmailText', 'text', array('required'=>false))

            ->add('file_adminEmailText', 'file',
                array(
                    'required'=>false,
                    'mapped'=>false,
                    'label'=>"or upload"))

            ->add('adminEmailHTML', 'text', array('required'=>false))

            ->add('file_adminEmailHTML', 'file',
                array(
                    'required'=>false,
                    'mapped'=>false,
                    'label'=>"or upload"))

            ->add('save', 'submit', array('label'=> 'save ' . $this->mode));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'JfxNinja\CMSBundle\Entity\CMSForm'
        ));
    }

    public function getName()
    {
        return 'CMSForm';
    }

}



