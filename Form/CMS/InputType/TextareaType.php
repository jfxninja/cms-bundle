<?php

namespace JfxNinja\CMSBundle\Form\CMS\InputType;

use JfxNinja\CMSBundle\Form\CMS\InputSetupOption\Translatable;
use JfxNinja\CMSBundle\Form\CMS\InputSetupOption\Cols;
use JfxNinja\CMSBundle\Form\CMS\InputSetupOption\Rows;

class TextareaType extends AbstractType {

    /**
     * @return string
     */
    public function  getName()
    {
        return 'Textarea';
    }

    /**
     * @return string
     */
    public function  getVariableName()
    {
        return 'textarea';
    }


    public function buildSetupOptions()
    {
        $this->addSetupOption(new Translatable());
        $this->addSetupOption(new Cols());
        $this->addSetupOption(new Rows());
    }

}

