<?php

namespace JfxNinja\CMSBundle\Form\CMS\InputSetupOption;

class ContentType extends AbstractSetupOption {

    private $contentTypeRepo;

    function __construct($contentTypeRepo)
    {
        $this->contentTypeRepo = $contentTypeRepo;
    }

    /**
     * @return string
     */
    public function  getName()
    {
        return 'content_type';
    }

    /**
     * @return string
     */
    public function  getVariableName()
    {
        return 'content_type';
    }

    /**
     * @return string
     */
    public function  getLabel()
    {
        return 'Content Type';
    }

    /**
     * @return string
     */
    public function  getInputType()
    {
        return 'entity';
    }

    /**
     * @return string
     */
    public function  getInputTypeVar()
    {
       return $this->contentTypeRepo->getContentTypeChoiceOptions();
    }

}

