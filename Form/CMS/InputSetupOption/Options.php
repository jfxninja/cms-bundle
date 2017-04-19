<?php

namespace JfxNinja\CMSBundle\Form\CMS\InputSetupOption;

class Options extends AbstractSetupOption {


    /**
     * @return string
     */
    public function  getName()
    {
        return 'options';
    }

    /**
     * @return string
     */
    public function  getVariableName()
    {
        return 'options';
    }

    /**
     * @return string
     */
    public function  getLabel()
    {
        return 'Options';
    }

    /**
     * @return string
     */
    public function  getInputType()
    {
        return 'textarea';
    }

    /**
     * @return string
     */
    public function  getInputTypeVar()
   {
       return;
   }

}

