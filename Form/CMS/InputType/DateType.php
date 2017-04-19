<?php

namespace JfxNinja\CMSBundle\Form\CMS\InputType;

use JfxNinja\CMSBundle\Form\CMS\InputSetupOption\DateStringFormat;

class DateType extends AbstractType {

    /**
     * @return string
     */
    public function  getName()
    {
        return 'Date';
    }

    /**
     * @return string
     */
    public function  getVariableName()
    {
        return 'date';
    }


    public function buildSetupOptions()
    {
        $this->addSetupOption(new DateStringFormat());
    }

}

