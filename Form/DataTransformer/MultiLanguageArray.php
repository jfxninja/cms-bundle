<?php

namespace JfxNinja\CMSBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;


class MultiLanguageArray implements DataTransformerInterface
{

    private $locale = array();

    public function __construct($locale){
        $this->locale = $locale;
    }

    public function transform($dbData)
    {

        //if the data is non-multilanguage return the data as is
        if($this->checkForNonMultilanguageData($dbData))
        {
            return $dbData;
        }

        //if there exists a translation return it
        if(isset($dbData[$this->locale]))
        {
            return $dbData[$this->locale];
        }
        //Otherwise return the default translation
        else
        {
            return "";
            //return $dbData['en']; //Not returning en as default as this is misleading for now
        }

    }


    public function reverseTransform($formData)
    {
        return array($this->locale=>$formData);
    }

    /**
     *
     * @param $dbData
     * @return bool (true = This data is stored as non multilanguage)
     */
    private function checkForNonMultilanguageData($dbData)
    {

        //1st check is it an array?
        if(!is_array($dbData))
        {
            return true;
        }
        else
        {
            foreach(array_keys($dbData) as $arrayKey)
            {
                if(!preg_match('/[a-z]{2}/', $arrayKey))
                {
                    return true;
                }
            }
            return false;
        }

    }

}