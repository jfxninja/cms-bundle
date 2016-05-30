<?php

namespace SSone\CMSBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class MenuItemTYPE extends AbstractType
{

    private $contentLibrary;
    private $menuItems;
    private $fieldsRepository;
    private $locale;
    private $mode;

    public function __construct($locale,$mode,$contentLibrary,$menuItems,$fieldsRepository){

        $this->mode = $mode;
        $this->locale = $locale;
        $this->contentLibrary = $contentLibrary;
        $this->menuItems = $menuItems;
        $this->fieldsRepository = $fieldsRepository;

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {



        //Build the options for the attach content choice field
        $attachContentOptions = $this->buildAttachContentChoiceOptions();

        //Create the form
        $builder
            ->add('name', 'multiLanguageText', array("locale"=>$this->locale))
            ->add('sort', 'text')
            ->add('domain_template_override', 'text',array('required'=>false))
            ->add('hide', 'checkbox', array('label' => 'Hide from menus'),array('required'=>false))
            ->add('slug', 'multiLanguageText', array("locale"=>$this->locale))
            ->add('pageClass', 'text', array('required'=>false))
            ->add('pageTitle', 'multiLanguageText', array("locale"=>$this->locale))
            ->add('metaDescription', 'multiLanguageTextarea', array("locale"=>$this->locale,'required'=>false))
            ->add('grandChildrenRelativePosition', 'choice',
                array('choices'=>array(
                    "inline"=>"Inline",
                    "separate"=>"Separate")))
            ->add('drawAllGrandChildren', 'choice', array('choices'=>array(1=>"Yes",0=>"No")))
            ->add('grandChildrenTemplatePosition', 'choice',
                array('choices'=>array(
                    "A"=>"Position A",
                    "B"=>"Position B",
                    "C"=>"Position C",
                    "D"=>"Position D",
                    "E"=>"Position E",
                    "F"=>"Position F",
                    "G"=>"Position G",
                    "H"=>"Position H")))


            ->add('mapAttached','choice',array(
                'label' => 'Parent Menu',
                'choices' => $this->menuItems,
            ))

            ->add('mapContent','choice',
                array(
                    'label'=>'Attach Content',
                    'choices'=>$attachContentOptions,
                    'required'=>false,
                ))
            ->add('drawListItemsAsMenuItems', 'checkbox', array('required'=>false))
            ;


            $mode = $this->mode;
            $fieldsRepository = $this->fieldsRepository;

            $addcategoryfields = function (FormEvent $e) use ($mode,$fieldsRepository) {

                $form = $e->getForm();
                $data = $e->getData();


                //Check data type different depending on event
                if(gettype($data) == "array")
                {
                    //get the menu type setting
                    $mapContent = $data['mapContent'];

                    //get the field object of category 2
                    if($data['contentCategory2'])
                    {
                    $ctcf2 = $fieldsRepository->fieldSettingsById($data['contentCategory2']);
                    }
                    else
                    {
                        $ctcf2 = "";
                    }
                }
                elseif(gettype($data) == "object")
                {
                    //get the menu type setting
                    $mapContent = $data->getMapContent();

                    //get the field object of category 2
                    if($data->getContentCategory2())
                    {
                        $ctcf2 = $fieldsRepository->fieldSettingsById($data->getContentCategory2()->getId());
                    }
                    else
                    {
                        $ctcf2 = "";
                    }

                }

                $mapContentOptions = explode("_",$mapContent);

                $ctId = (strpos($mapContent,"list") !== false) ? $mapContentOptions[1] : 0;

                $ctcf = $fieldsRepository->getContentTypeCategoryFieldsAsObjects($ctId);


                if($ctcf2)
                {
                    $category2ContentTypeId = $ctcf2['fieldTypeSettings']['relatedcontent']['relatedcontent'];
                    $category2RelatedChoices = $fieldsRepository->getContentTypeCategoryFieldsAsObjects($category2ContentTypeId);
                    $emptyValue = "Choose an option" ;
                }
                else
                {
                    $category2RelatedChoices = array();
                    $emptyValue = "N/A";
                }
                //Get eth content Type
                //Get the fields

                $form
                    ->add('contentCategory1','entity',array(
                        'class' => 'SSoneCMSBundle:Field',
                        'property' => 'name',
                        'required' => false,
                        'empty_value' => 'Choose an option',
                        'label' => 'Categorized first by content type',
                        'choices' => $ctcf
                    ))

                    ->add('contentCategory2','entity',array(
                        'class' => 'SSoneCMSBundle:Field',
                        'property' => 'name',
                        'empty_value' => 'Choose an option',
                        'required' => false,
                        'label' => 'Categorized second by content type',
                        'choices' => $ctcf
                    ))

                    ->add('contentCategoryRelationship','entity',array(
                        'class' => 'SSoneCMSBundle:Field',
                        'property' => 'name',
                        'empty_value' => $emptyValue,
                        'required' => false,
                        'label' => 'Category 2 is related to category 1 by field',
                        'choices' => $category2RelatedChoices
                    ))
                    ->add('hideEmptyCategories', 'choice', array('choices'=>array(1=>"Yes",0=>"No")))

                    ->add('save', 'submit', array('label'=> 'save ' . $mode));
            };

            $builder->addEventListener(FormEvents::PRE_SET_DATA, $addcategoryfields);
            $builder->addEventListener(FormEvents::PRE_SUBMIT, $addcategoryfields);

    }



    public function buildAttachContentChoiceOptions()
    {

        $attachOptions = array();

        foreach($this->contentLibrary as $ctId=>$contentType)
        {
            $attachOptions[$contentType['name']]['list_'.$ctId] =  'List ' . $contentType['name'];

            foreach($contentType['items'] as $item)
            {
                $attachOptions[$contentType['name']]['single_'.$item['id']] = $item['name'];
            }


        }

        return $attachOptions;

    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SSone\CMSBundle\Entity\MenuItem'
        ));
    }

    public function getName()
    {
        return 'menuItem';
    }

}



