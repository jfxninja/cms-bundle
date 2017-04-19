<?php

namespace JfxNinja\CMSBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class ModuleTYPE extends AbstractType
{

    private $locale;
    private $em;
    private $contentService;

    public function __construct($em,$contentService,$locale){

        $this->locale = $locale;
        $this->em = $em;
        $this->contentService = $contentService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', 'text')
            ->add('title', 'multiLanguageText', array("locale"=>$this->locale))
            ->add('description', 'multiLanguageTextarea', array("locale"=>$this->locale))
            ->add('sort', 'text')
            ->add('urlMatchExpression', 'multiLanguageText', array("locale"=>$this->locale))
            ->add('templatePosition', 'choice',
                array('choices'=>array(
                    "A"=>"Position A",
                    "B"=>"Position B",
                    "C"=>"Position C",
                    "D"=>"Position D",
                    "E"=>"Position E",
                    "F"=>"Position F",
                    "G"=>"Position G",
                    "H"=>"Position H")))

            ->add('templatePath', 'text', array('required'=>false))
            ->add('file_templatePath', 'file',
                array(
                    'required'=>false,
                    'mapped'=>false,
                    'label'=>"or upload"))

            ->add('type', 'choice',
                array('choices'=>array(
                    "content"=>"Content",
                    "form"=>"Form")))

            ->add('form', 'entity', array(
                    'class' => 'JfxNinjaCMSBundle:CMSForm',
                    'property' => "name",
                    'required'=>false,

                    ))
            ->add('contentType', 'entity', array(
                    'class' => 'JfxNinjaCMSBundle:ContentType',
                    'property' => "name",
                    'required'=>false,
                    'query_builder' => function(EntityRepository $er) {
                            return $er->createQueryBuilder('c')
                                ->add('where', 'c.hideFromMenus != 1')
                                ->orderBy('c.name', 'ASC');
                        },
                    ))

        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));


    }

    protected function addQueryFields($form, $contentType, $contentFilterFieldID)
    {

        $filterFields = array();
        $filterValueOptions = array();
        $contentItems = array();
        $sortFields = array();

        if($contentType)
        {

            $fieldsRepository = $this->em->getRepository('JfxNinjaCMSBundle:Field');

            //set fields
            $filterFields = $fieldsRepository->getContentTypeFields($contentType->getId(),array("related_content","text","checkbox"));

            //set fields
            $contentItems = $contentType->getContent();

            $sortFields =  $fieldsRepository->getContentTypeFields($contentType->getId(),array("related_content","text","date"));

        }

        $filterField = array();

        if($contentFilterFieldID)
        {
            $filterField = $this->em->getRepository('JfxNinjaCMSBundle:Field')->find($contentFilterFieldID);

            if($filterField && $filterField->getType()->getVariableName() == "related_content")
            {
                $settings = $filterField->getFieldTypeSettings();
                $filterValueOptions = $this->contentService->findContentByContentTypeIdChoiceFormatted($settings['related_content__content_type']);

            }

        }

        $form
            ->add('singleContentItem','entity',array(
                'class' => 'JfxNinjaCMSBundle:Content',
                'property' => 'name',
                'empty_value' => 'Choose an option',
                'required' => false,
                'label' => 'Attach single item?',
                'choices' => $contentItems
            ))

            ->add('contentFilterField', 'choice',
                array(
                    'choices'=>$filterFields,
                    'required'=>false,
                    'label' => 'Filter list by field')
            );

        if($filterField && $filterField->geType() == "related_content")
        {
            $form
                ->add('contentFilterValue', 'choice',
                    array('choices'=>$filterValueOptions,'required' => false,)
                );
        }
        else
        {
            $form
                ->add('contentFilterValue', 'text',array('required' => false));
        }


        $form->add('contentOrderByField', 'choice', array(
            "choices" => $sortFields
        ));

        $form->add('contentOrderByValue', 'choice', array(
            "choices" => array('asc'=>'Ascending', 'dec'=>'Descending')
        ));

        $form
            ->add('save', 'submit', array('label'=> 'save'));



    }

    function onPreSubmit(FormEvent $event) {
        $form = $event->getForm();
        $data = $event->getData();

        // Note that the data is not yet hydrated into the entity.
        $contentType = $this->em->getRepository('JfxNinjaCMSBundle:ContentType')->find($data['contentType']);
        if(isset($data['contentFilterField']))
        {
            $contentFilterField = $this->em->getRepository('JfxNinjaCMSBundle:Field')->find($data['contentFilterField']);
        }
        else
        {
            $contentFilterField = array();
        }

        $this->addQueryFields($form, $contentType, $contentFilterField);
    }


    function onPreSetData(FormEvent $event) {

        $module = $event->getData();
        $form = $event->getForm();


        // We might have an empty account (when we insert a new account, for instance)
        $contentType = $module->getContentType() ? $module->getContentType() : null;
        $contentFilterField = $module->getContentFilterField() ? $module->getContentFilterField() : null;

        $this->addQueryFields($form, $contentType, $contentFilterField);

    }





    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'JfxNinja\CMSBundle\Entity\Module'
        ));
    }

    public function getName()
    {
        return 'module';
    }

}



