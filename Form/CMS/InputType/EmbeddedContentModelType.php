<?php

namespace JfxNinja\CMSBundle\Form\CMS\InputType;

use JfxNinja\CMSBundle\Form\CMS\InputSetupOption\ContentType;

class EmbeddedContentModelType extends AbstractType {

    private $contentTypeRepo;

    function __construct($contentTypeRepo) {

        $this->contentTypeRepo = $contentTypeRepo;
        $this->buildSetupOptions();
    }

    /**
     * @return string
     */
    public function  getName()
    {
        return 'Embedded content model';
    }

    /**
     * @return string
     */
    public function  getVariableName()
    {
        return 'embedded_content_model';
    }


    public function buildSetupOptions()
    {
        $this->addSetupOption(new ContentType($this->contentTypeRepo));
    }

}

