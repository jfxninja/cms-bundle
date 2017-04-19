<?php

namespace JfxNinja\CMSBundle\Form\CMS\InputSetupOption;

class Translatable extends AbstractSetupOption {


    /**
     * @return string
     */
    public function  getName()
    {
        return 'translatable';
    }

    /**
     * @return string
     */
    public function  getVariableName()
    {
        return 'translatable';
    }

    /**
     * @return string
     */
    public function  getLabel()
    {
        return 'Is this field translatable?';
    }

    /**
     * @return string
     */
    public function  getInputType()
    {
        return 'checkbox';
    }

    /**
     * @return string
     */
    public function  getInputTypeVar()
   {
       return;
   }

}

