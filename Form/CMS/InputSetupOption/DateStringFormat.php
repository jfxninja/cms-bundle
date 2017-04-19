<?php

namespace JfxNinja\CMSBundle\Form\CMS\InputSetupOption;

class DateStringFormat extends AbstractSetupOption {


    /**
     * @return string
     */
    public function  getName()
    {
        return 'date_string_format';
    }

    /**
     * @return string
     */
    public function  getVariableName()
    {
        return 'date_string_format';
    }

    /**
     * @return string
     */
    public function  getLabel()
    {
        return 'Date format';
    }

    /**
     * @return string
     */
    public function  getInputType()
    {
        return 'text';
    }

    /**
     * @return string
     */
    public function  getInputTypeVar()
   {
       return;
   }

}

