<?php

namespace SSone\CMSBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;


class CMSChoiceFieldTransformer implements DataTransformerInterface
{


    private $options;

    public function __construct($options){

        $this->options = $options;

    }

    public function transform($dbData)
    {

        //Handle change of field settings from single choice to multi choice
        if($this->options['multiple'])
        {
            if(!is_array($dbData))
            {
                return array($dbData);
            }
        }

        return $dbData;
    }


    public function reverseTransform($formData)
    {
        return $formData;
    }
}