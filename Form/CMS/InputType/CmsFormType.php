<?php

namespace JfxNinja\CMSBundle\Form\CMS\InputType;

use JfxNinja\CMSBundle\Form\CMS\InputSetupOption\Translatable;

class CmsFormType extends AbstractType {

    /**
     * @return string
     */
    public function  getName()
    {
        return 'CMS Form';
    }

    /**
     * @return string
     */
    public function  getVariableName()
    {
        return 'cms_form';
    }


    public function buildSetupOptions()
    {
        $this->addSetupOption(new Translatable());
    }

}

