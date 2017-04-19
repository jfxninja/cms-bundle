<?php

namespace JfxNinja\CMSBundle\Form\CMS\InputType;

use JfxNinja\CMSBundle\Form\CMS\InputSetupOption\Options;
use JfxNinja\CMSBundle\Form\CMS\InputSetupOption\ChoiceExpanded;
use JfxNinja\CMSBundle\Form\CMS\InputSetupOption\ChoiceMulti;

class ChoiceType extends AbstractType {

    /**
     * @return string
     */
    public function  getName()
    {
        return 'Choice';
    }

    /**
     * @return string
     */
    public function  getVariableName()
    {
        return 'choice';
    }


    public function buildSetupOptions()
    {
        $this->addSetupOption(new Options());
        $this->addSetupOption(new ChoiceExpanded());
        $this->addSetupOption(new ChoiceMulti());
    }

}

