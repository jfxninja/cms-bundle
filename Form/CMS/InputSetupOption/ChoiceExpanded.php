<?php

namespace JfxNinja\CMSBundle\Form\CMS\InputSetupOption;

class ChoiceExpanded extends AbstractSetupOption {


    /**
     * @return string
     */
    public function  getName()
    {
        return 'choice_expanded';
    }

    /**
     * @return string
     */
    public function  getVariableName()
    {
        return 'choice_expanded';
    }

    /**
     * @return string
     */
    public function  getLabel()
    {
        return 'Choice Expanded';
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

