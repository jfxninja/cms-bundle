<?php

namespace JfxNinja\CMSBundle\Services;

use JfxNinja\CMSBundle\Entity\ContentTypesRepository;

use JfxNinja\CMSBundle\Form\CMS\InputType\CheckboxType;
use JfxNinja\CMSBundle\Form\CMS\InputType\ChoiceType;
use JfxNinja\CMSBundle\Form\CMS\InputType\CmsFormType;
use JfxNinja\CMSBundle\Form\CMS\InputType\DateType;
use JfxNinja\CMSBundle\Form\CMS\InputType\EmbeddedContentModelType;
use JfxNinja\CMSBundle\Form\CMS\InputType\RelatedContentType;
use JfxNinja\CMSBundle\Form\CMS\InputType\WysiwygType;
use JfxNinja\CMSBundle\Form\CMS\InputType\TextareaType;
use JfxNinja\CMSBundle\Form\CMS\InputType\TextType;
use JfxNinja\CMSBundle\Form\CMS\InputType\FileUploadType;

class CmsInputTypeService
{

    private $contentTypeRepo;

    function __construct(ContentTypesRepository $contentTypeRepo)
    {
        $this->contenTypeRepo = $contentTypeRepo;
    }

    /**
     * @return mixed
     */
    public function getInputTypeSetupOptions()
    {
        foreach( $this->getInputTypes() as $type)
        {
            $options[$type->getVariableName()] = $type->getSetupOptions();
        }

        return $options;
    }

    /**
     * @return array
     */
    public function getInputTypes()
    {
        return array(
            new CheckboxType(),
            new ChoiceType(),
            new CmsFormType(),
            new DateType(),
            new EmbeddedContentModelType($this->contenTypeRepo),
            new FileUploadType(),
            new RelatedContentType($this->contenTypeRepo),
            new TextareaType(),
            new TextType(),
            new WysiwygType(),
        );
    }

}
