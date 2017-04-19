<?php

namespace JfxNinja\CMSBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use JfxNinja\CMSBundle\Form\Type\Field\FieldTypeNested;

class ContentTypeTYPE extends AbstractType
{

    private $mode;
    private $locale;
    private $inputTypes;
    private $inputSetupOptions;

    public function __construct($mode,$inputTypes,$inputSetupOptions,$locale){
        $this->mode = $mode;
        $this->locale = $locale;
        $this->inputTypes = $inputTypes;
        $this->inputSetupOptions = $inputSetupOptions;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('slug', 'multiLanguageText', array("locale"=>$this->locale))
            ->add('hideFromMenus', 'checkbox',array("required"=>false))
            ->add('file_contentTemplatePath', 'file',
                array(
                    'required'=>false,
                    'mapped'=>false,
                    'label'=>"or upload"))
            ->add('contentTemplatePath', 'text', array('required'=>false))

            ->add('file_listTemplatePath', 'file',
                array(
                    'required'=>false,
                    'mapped'=>false,
                    'label'=>"or upload"))
            ->add('listTemplatePath', 'text', array('required'=>false))

            ->add('file_categoryPageTemplatePath', 'file',
                array(
                    'required'=>false,
                    'mapped'=>false,
                    'label'=>"or upload"))
            ->add('categoryPageTemplatePath', 'text',array('required'=>false))

            ->add('attributeFields', 'collection', array(
                'type' => new FieldTypeNested($this->inputTypes,$this->inputSetupOptions),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false
            ))
            ->add('variableFields', 'collection', array(
                'type' => new FieldTypeNested($this->inputTypes,$this->inputSetupOptions),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false
            ))
            ->add('save', 'submit', array('label'=> 'save ' . $this->mode));

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'JfxNinja\CMSBundle\Entity\ContentType'
        ));
    }

    public function getName()
    {
        return 'contentType';
    }

}



