<?php

namespace JfxNinja\CMSBundle\Form\CMS\InputType;


class CheckboxType extends AbstractType {

    /**
     * @return string
     */
    public function  getName()
    {
        return 'Checkbox';
    }

    /**
     * @return string
     */
    public function  getVariableName()
    {
        return 'checkbox';
    }

}

