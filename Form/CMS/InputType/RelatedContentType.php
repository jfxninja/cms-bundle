<?php

namespace JfxNinja\CMSBundle\Form\CMS\InputType;

use JfxNinja\CMSBundle\Form\CMS\InputSetupOption\ContentType;

class RelatedContentType extends AbstractType {

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
        return 'Related content';
    }

    /**
     * @return string
     */
    public function  getVariableName()
    {
        return 'related_content';
    }


    public function buildSetupOptions()
    {
        $this->addSetupOption(new ContentType($this->contentTypeRepo));
    }

}

