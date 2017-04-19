<?php

namespace JfxNinja\CMSBundle\Form\Type\Block;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class BlockFieldInputsBuilder extends AbstractType
{
    private $field;
    private $options;
    private $fieldsRepository;
    private $cs;
    private $locale;

    public function __construct($field, $fieldsRepository,$cs,$locale){

        $this->field = $field;
        $this->fieldsRepository = $fieldsRepository;
        $this->cs = $cs;
        $this->locale = $locale;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $this->options = $options;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));

    }

    public function onPreSetData(FormEvent $event){

        $form = $event->getForm();

        if($this->field->getType() == "embedded_content_model")
        {
            $fieldSettings = $this->field->getFieldTypeSettings();

            $id = $fieldSettings['embedded_content_model__content_type'];

            $fields = $this->fieldsRepository->findByContentTypeId($id);

        }
        else
        {
            $fields[] = $this->field;
        }

        foreach($fields as $field)
        {
            $this->buildField($form,$field,$this->options);
        }



    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'fieldType' => '' )
        );
    }


    public function getName()
    {
        return 'dynamicCMSfield';
    }

    private function convertChoiceOptionsStringToArray($stringOptions)
    {
        $options = array();

        $stringOptions = rtrim($stringOptions, ";");


        foreach(explode(";",$stringOptions) as $stringOption)
        {

            $arrayOption = explode("=",$stringOption);

            if(trim($arrayOption[0]))
            {
                $options[trim($arrayOption[0])] = trim($arrayOption[1]);
            }


        }
        return $options;
    }

    private function buildField($form,$field,$options)
    {

        $variableName = $field->getVariableName();
        $label = $field->getLabel();
        $constraints = array();
        if($field->getIsRequired())
        {
            $requiredText = $field->getRequiredText();

            if(!$requiredText) $requiredText = "This is required information";
            $constraints = array(new NotBlank(array("message"=>$requiredText)));
        }


        $fieldSettings = array();

        if($field->getFieldTypeSettings() != null)
        {
            $fieldSettings = $field->getFieldTypeSettings();
        }

        $securekey = $field->getSecurekey();

        switch($field->getType())
        {
            case "text":
                if($fieldSettings['text__translatable'])
                {
                    $form->add($variableName, 'multiLanguageText',
                        array(
                            'label'=>$label,
                            "locale"=>$this->locale,
                            "required"=>false,
                            "constraints"=>$constraints
                        )
                    );
                }
                else
                {
                    $form->add($variableName, 'text',
                        array(
                            'label'=>$label,
                            "required"=>false,
                            "constraints"=>$constraints
                        )
                    );
                }

                break;

            case "textarea":

                if($fieldSettings['textarea__translatable'])
                {

                    $form->add($variableName, 'multiLanguageTextarea',
                        array(
                            'label'=>$label,
                            "locale"=>$this->locale,
                            "required"=>false,
                            'constraints' => $constraints,
                            "rows"=>$fieldSettings['textarea__rows'],
                            "cols"=>$fieldSettings['textarea__cols'],

                        ));
                }
                else
                {
                    $form->add($variableName, 'textarea',
                        array(
                            'label'=>$label,
                            "required"=>false,
                            'constraints' => $constraints,
                            "rows"=>$fieldSettings['textarea__cols'],
                            "cols"=>$fieldSettings['textarea__rows']
                        ));
                }
                break;

            case "filesfolder":
                $form
                    ->add($variableName, 'text',
                        array(
                            'label'=>$label,
                            "required"=>false,
                            'constraints' => $constraints,
                        ));
                break;

            case "file_upload":
                $form
                    ->add($variableName, 'CMSFieldFilePath',
                        array(
                            'label'=>$label.' (file path assets/[path])',
                            "required"=>false,
                        ));
                $form
                    ->add($variableName.'_fileupload_'.$securekey, 'file',
                        array(
                            'label'=>'Or upload',
                            'mapped'=>false,
                            "required"=>false,
                            //'constraints' => $constraints,
                        ));

                break;

            case "choice":
                $options = $this->convertChoiceOptionsStringToArray($fieldSettings['choice__options']);
                $form
                    ->add($variableName, 'CMSChoice',
                        array(
                            'choices'=>$options,
                            'label'=>$label,
                            'expanded'=>$fieldSettings['choice__choice_expanded'],
                            'multiple'=>$fieldSettings['choice__choice_multi'],
                            "required"=>false,
                            'constraints' => $constraints,
                        )
                    );
                break;


            case "checkbox":
                $form
                    ->add($variableName, 'checkbox',
                        array(
                            'label'=>$label,
                            "required"=>false,
                            'constraints' => $constraints,
                        ));
                break;

            case "date":
                $form
                    ->add($variableName, 'date',
                        array(
                            'input'=>'string',
                            'widget'=>'single_text',
                            'label'=>$label,
                            "required"=>false,
                            'constraints' => $constraints,
                        ));
                break;

            case "related_content":

                $options = array(""=>"-");
                foreach($this->cs->findContentByContentTypeId($fieldSettings['related_content__content_type']) as $content)
                {
                    $options[$content['id']] = $content['name'];
                }


                $form
                    ->add($variableName, 'choice',
                            array(
                                'choices'=>$options,
                                'label'=>$label,
                                'expanded'=>false,
                                'multiple'=>false,
                                "required"=>false,
                                'constraints' => $constraints,
                            )
                    );
                break;

            case "wysiwyg_editor":

                if(isset($fieldSettings['wysiwyg_editor__translatable']) && $fieldSettings['wysiwyg_editor__translatable'])
                {
                    $form->add($variableName, 'multiLanguageTextarea',
                        array(
                            'label'=>$label,
                            "locale"=>$this->locale,
                            "required"=>false,
                            'attr' => array(
                                'class'=>'wysiwyg-field',
                                'data-options'=> $fieldSettings['wysiwyg_editor__options'],
                                'sfid'=> $securekey
                            ),
                            'constraints' => $constraints,

                        ));
                }
                else
                {
                    $form->add($variableName, 'textarea',
                        array(
                            'label'=>$label,
                            'attr' => array(
                                'class'=>'wysiwyg-field',
                                'data-options'=> $fieldSettings['wysiwyg_editor__options'],
                                'sfid'=> $securekey
                            ),
                            "required"=>false,
                            "constraints"=>$constraints
                        )
                    );
                }
                break;

            case "cms_form":

                $options = $this->cs->getFormsAsChoiceOptions();
                if($fieldSettings['cms_form__translatable'])
                {
                $form
                    ->add($variableName, 'multiLanguageChoice',
                            array(
                                'choices'=>$options,
                                'label'=>$label,
                                'expanded'=>false,
                                "locale"=>$this->locale,
                                'multiple'=>false,
                                "required"=>false,
                                'constraints' => $constraints,
                            )
                    );
                }
                else
                {
                    $form
                        ->add($variableName, 'choice',
                            array(
                                'choices'=>$options,
                                'label'=>$label,
                                'expanded'=>false,
                                'multiple'=>false,
                                "required"=>false,
                                'constraints' => $constraints,
                            )
                        );
                }


                break;
        }


    }

}



