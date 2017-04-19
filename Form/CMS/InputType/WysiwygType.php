<?php

namespace JfxNinja\CMSBundle\Form\CMS\InputType;

use JfxNinja\CMSBundle\Form\CMS\InputSetupOption\FileUploadFolder;
use JfxNinja\CMSBundle\Form\CMS\InputSetupOption\Options;
use JfxNinja\CMSBundle\Form\CMS\InputSetupOption\Translatable;

class WysiwygType extends AbstractType {

    /**
     * @return string
     */
    public function  getName()
    {
        return 'CWYSIWYG Editor';
    }

    /**
     * @return string
     */
    public function  getVariableName()
    {
        return 'wysiwyg_editor';
    }


    public function buildSetupOptions()
    {
        $this->addSetupOption(new Translatable());
        $this->addSetupOption(new Options());
        $this->addSetupOption(new FileUploadFolder());
    }

}

