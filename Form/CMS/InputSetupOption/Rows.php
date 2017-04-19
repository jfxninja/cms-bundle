<?php

namespace JfxNinja\CMSBundle\Form\CMS\InputSetupOption;

class Rows extends AbstractSetupOption {


    /**
     * @return string
     */
    public function  getName()
    {
        return 'rows';
    }

    /**
     * @return string
     */
    public function  getVariableName()
    {
        return 'rows';
    }

    /**
     * @return string
     */
    public function  getLabel()
    {
        return 'Textarea Rows';
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

