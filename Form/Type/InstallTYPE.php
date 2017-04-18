<?php

namespace JfxNinja\CMSBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InstallTYPE extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text')
            ->add('email', 'text')
            ->add('password', 'password')
            ->add('domain', 'text')
            ->add('dev_domain', 'text')
            ->add('save', 'submit', array('label'=> 'Install'));
    }

    public function getName()
    {
        return 'install';
    }
}