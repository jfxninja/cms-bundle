<?php

namespace JfxNinja\CMSBundle\Form\Type\Block;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class BlockTYPE extends AbstractType
{

    private $locale;
    private $fieldsRepository;
    private $CMSFormService;

    public function __construct($locale,$fieldsRepository,$cs){
        $this->locale = $locale;
        $this->fieldsRepository = $fieldsRepository;
        $this->cs = $cs;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = $this->locale;
        $fieldsRepository = $this->fieldsRepository;
        $cs = $this->cs;
        $CMSFormService = $this->CMSFormService;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($locale,$fieldsRepository,$cs,$CMSFormService) {

            $form = $event->getForm();

            $block = $event->getData();

            $field = $block->getField();

            $isRepeatable = $field->getIsRepeatable();

            $label = ($isRepeatable)?$field->getRepeatableGroupLabel():false;

            $form
                ->add('blockFields','collection', array(
                    'label' => $label,
                    'type' => new BlockFieldBuilder($field, $fieldsRepository, $cs, $locale),
                    'allow_add' => $isRepeatable,
                    'allow_delete' => $isRepeatable,
                    'by_reference' => false,
                ));

        });


    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'jfxninja\CMSBundle\Entity\Block'
        ));
    }

    public function getName()
    {
        return 'blockType';
    }

}



