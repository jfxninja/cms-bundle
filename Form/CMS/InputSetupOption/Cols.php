<?php

namespace JfxNinja\CMSBundle\Form\CMS\InputSetupOption;

class Cols extends AbstractSetupOption {


    /**
     * @return string
     */
    public function  getName()
    {
        return 'cols';
    }

    /**
     * @return string
     */
    public function  getVariableName()
    {
        return 'cols';
    }

    /**
     * @return string
     */
    public function  getLabel()
    {
        return 'Textarea Columns';
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

