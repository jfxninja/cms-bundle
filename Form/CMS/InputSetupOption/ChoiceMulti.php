<?php

namespace JfxNinja\CMSBundle\Form\CMS\InputSetupOption;

class ChoiceMulti extends AbstractSetupOption {


    /**
     * @return string
     */
    public function  getName()
    {
        return 'choice_multi';
    }

    /**
     * @return string
     */
    public function  getVariableName()
    {
        return 'choice_multi';
    }

    /**
     * @return string
     */
    public function  getLabel()
    {
        return 'Choice Multi';
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

