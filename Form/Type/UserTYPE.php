<?php

namespace JfxNinja\CMSBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserTYPE extends AbstractType
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
            ->add('username', 'text')
            ->add('email', 'text')
            ->add('password', 'repeated',
            array(
                'first_name' => 'password',
                'second_name' => 'confirm',
                'type' => 'password',
                "mapped" => false,
                "required" => false,
            ))

            ->add('save', 'submit', array('label'=> 'save ' . $this->mode));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'JfxNinja\CMSBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'user';
    }

}



