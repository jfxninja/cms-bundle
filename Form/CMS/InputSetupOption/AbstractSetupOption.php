<?php

namespace JfxNinja\CMSBundle\Form\CMS\InputSetupOption;



abstract class AbstractSetupOption {

    /**
     * @return string
     */
    abstract function  getName();

    /**
     * @return string
     */
    abstract function  getVariableName();

    /**
     * @return string
     */
    abstract function  getLabel();

    /**
     * @return string
     */
    abstract function  getInputType();

    /**
     * @return string
     */
    abstract function  getInputTypeVar();


}

