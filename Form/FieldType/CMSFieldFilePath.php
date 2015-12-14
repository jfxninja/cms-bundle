<?php

namespace SSone\CMSBundle\Form\FieldType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use SSone\CMSBundle\Form\DataTransformer\CMSImageFieldPathTransformer;



class CMSFieldFilePath extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addModelTransformer(new CMSImageFieldPathTransformer());

    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'CMSFieldFilePath';
    }

}



