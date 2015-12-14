<?php

namespace SSone\CMSBundle\Form\Type\Block;

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

        if($this->field->getFieldType()->getVariableName() == "embeded")
        {
            $fieldSettings = $this->field->getFieldTypeSettings();

            $id = $fieldSettings['embeded']['contenttype'];

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
                $options[trim($arrayOption[1])] = trim($arrayOption[0]);
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

        switch($field->getFieldType()->getVariableName())
        {
            case "text":
                if($fieldSettings['text']['texttrans'])
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

                if($fieldSettings['textarea']['textareatrans'])
                {

                    $form->add($variableName, 'multiLanguageTextarea',
                        array(
                            'label'=>$label,
                            "locale"=>$this->locale,
                            "required"=>false,
                            'constraints' => $constraints,
                            "rows"=>$fieldSettings['textarea']['textareaRows'],
                            "cols"=>$fieldSettings['textarea']['textareaCols'],

                        ));
                }
                else
                {
                    $form->add($variableName, 'textarea',
                        array(
                            'label'=>$label,
                            "required"=>false,
                            'constraints' => $constraints,
                            "rows"=>$fieldSettings['textarea']['textareaRows'],
                            "cols"=>$fieldSettings['textarea']['textareaCols']
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

            case "fileupload":
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
                $options = $this->convertChoiceOptionsStringToArray($fieldSettings['choice']['choiceoptions']);
                $form
                    ->add($variableName, 'CMSChoice',
                        array(
                            'choices'=>$options,
                            'label'=>$label,
                            'expanded'=>$fieldSettings['choice']['choiceexp'],
                            'multiple'=>$fieldSettings['choice']['choicemulti'],
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

            case "relatedcontent":

                $options = array(""=>"-");
                foreach($this->cs->findContentByContentTypeId($fieldSettings['relatedcontent']['relatedcontent']) as $content)
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

            case "wysiwyg":

                $form->add($variableName, 'textarea',
                    array(
                        'label'=>$label,
                        'attr' => array(
                            'class'=>'wysiwyg-field',
                            'data-options'=> $fieldSettings['wysiwyg']['wysiwygsetupoptions'],
                            'sfid'=> $securekey
                        ),
                        "required"=>false,
                        "constraints"=>$constraints
                    )
                );


                break;

            case "form":

                $options = $this->cs->getFormsAsChoiceOptions();
                if($fieldSettings['form']['multilanguageform'])
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



