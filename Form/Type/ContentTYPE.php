<?php

namespace JfxNinja\CMSBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityRepository;

use JfxNinja\CMSBundle\Form\Type\Block\BlockTYPE;

class ContentTYPE extends AbstractType
{

    private $mode;
    private $locale;
    private $cs;
    private $CMSFormService;
    private $fieldsRepository;

    public function __construct($mode,$fieldsRepository,$cs,$CMSFormService,$locale){
        $this->mode = $mode;
        $this->locale = $locale;
        $this->cs = $cs;
        $this->CMSFormService = $CMSFormService;
        $this->fieldsRepository = $fieldsRepository;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));

    }


    public function onPreSetData(FormEvent $event){

        $form = $event->getForm();

        $form->add('name', 'text');
        $form->add('slug', 'multiLanguageText', array("locale"=>$this->locale));

        //Content type is not set so this must be a new.
        if($this->mode == "new")
        {
            $form
            ->add('contentType','entity',array(
            'class' => 'JfxNinjaCMSBundle:ContentType',
            'property' => 'name',
            'label' => 'Content Type',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('c')
                    ->add('where', 'c.hideFromMenus != 1')
                    ->orderBy('c.name', 'ASC');
                }
            ));
        }
        //Otherwise draw the blocks
        else
        {
            $form
            ->add('blocks', 'collection', array(
            'type' => new BlockTYPE($this->locale, $this->fieldsRepository, $this->cs, $this->CMSFormService),
        ));

        }

        $form->add('save', 'submit', array('label'=> 'save ' . $this->mode));
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'jfxninja\CMSBundle\Entity\Content'
        ));
    }


    public function getName()
    {
        return 'content';
    }

}



