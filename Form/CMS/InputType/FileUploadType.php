<?php

namespace JfxNinja\CMSBundle\Form\CMS\InputType;

use JfxNinja\CMSBundle\Form\CMS\InputSetupOption\FileUploadFolder;

class FileUploadType extends AbstractType {

    /**
     * @return string
     */
    public function  getName()
    {
        return 'File upload';
    }

    /**
     * @return string
     */
    public function  getVariableName()
    {
        return 'file_upload';
    }


    public function buildSetupOptions()
    {
        $this->addSetupOption(new FileUploadFolder());
    }

}

