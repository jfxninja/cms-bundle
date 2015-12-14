<?php

namespace SSone\CMSBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;


class CMSImageFieldPathTransformer implements DataTransformerInterface
{



    public function __construct(){

    }

    public function transform($dbData)
    {

        $filePath = isset($dbData['filePath']) ? $dbData['filePath']: "";

        return $filePath;

    }


    public function reverseTransform($formData)
    {
        return $formData;
    }
}