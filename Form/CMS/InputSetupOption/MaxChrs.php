<?php

namespace JfxNinja\CMSBundle\Form\CMS\InputSetupOption;

class MaxChrs extends AbstractSetupOption {


    /**
     * @return string
     */
    public function  getName()
    {
        return 'maxchrs';
    }

    /**
     * @return string
     */
    public function  getVariableName()
    {
        return 'maxchrs';
    }

    /**
     * @return string
     */
    public function  getLabel()
    {
        return 'Max characters';
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

