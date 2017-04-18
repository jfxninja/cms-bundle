<?php

namespace JfxNinja\CMSBundle\Form\FieldType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use JfxNinja\CMSBundle\Form\DataTransformer\CMSImageFieldPathTransformer;



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



