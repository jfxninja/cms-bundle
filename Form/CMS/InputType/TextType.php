<?php

namespace JfxNinja\CMSBundle\Form\CMS\InputType;

use JfxNinja\CMSBundle\Form\CMS\InputSetupOption\Translatable;
use JfxNinja\CMSBundle\Form\CMS\InputSetupOption\MaxChrs;

class TextType extends AbstractType {

    /**
     * @return string
     */
    public function  getName()
    {
        return 'Text';
    }

    /**
     * @return string
     */
    public function  getVariableName()
    {
        return 'text';
    }


    public function buildSetupOptions()
    {
        $this->addSetupOption(new Translatable());
        $this->addSetupOption(new MaxChrs());
    }

}

