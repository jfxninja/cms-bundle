<?php

namespace JfxNinja\CMSBundle\Form\CMS\InputSetupOption;

class FileUploadFolder extends AbstractSetupOption {


    /**
     * @return string
     */
    public function  getName()
    {
        return 'file_upload_folder';
    }

    /**
     * @return string
     */
    public function  getVariableName()
    {
        return 'file_upload_folder';
    }

    /**
     * @return string
     */
    public function  getLabel()
    {
        return 'File upload folder';
    }

    /**
     * @return string
     */
    public function  getInputType()
    {
        return 'text';
    }

    /**
     * @return string
     */
    public function  getInputTypeVar()
   {
       return;
   }

}

