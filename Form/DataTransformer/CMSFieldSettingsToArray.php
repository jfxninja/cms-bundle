<?php

namespace SSone\CMSBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;


class CMSFieldSettingsToArray implements DataTransformerInterface
{

    private $fieldSetupOptions = array();

    public function __construct($fieldSetupOptions){
        $this->fieldSetupOptions = $fieldSetupOptions;
    }

    public function transform($fieldTypeSetting)
    {

        $settings = array();
        if($fieldTypeSetting === null) return $settings;

        foreach($fieldTypeSetting as $fieldTypeSettingsGroup)
        {
            foreach($fieldTypeSettingsGroup as $k => $setting)
            $settings[$k] = $setting;
        }

        return $settings;
    }


    public function reverseTransform($flatSettings)
    {

       $groupedSettings = array();

        foreach($this->fieldSetupOptions as $setupSettingsGroup)
        {
            foreach($setupSettingsGroup['options'] as $setupSetting)
            {
                foreach($flatSettings as $sk => $setting)
                {
                    if($setupSetting['variableName'] == $sk)
                        $groupedSettings[$setupSettingsGroup['fieldTypeVariableName']][$sk] = $setting;
                }
            }
        }
        return $groupedSettings;
    }
}