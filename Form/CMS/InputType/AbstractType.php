<?php

namespace JfxNinja\CMSBundle\Form\CMS\InputType;


abstract class AbstractType {

    private $setupOptions;

    function __construct() {
        $this->buildSetupOptions();
    }

    /**
     * @return string
     */
    abstract protected function  getName();

    /**
     * @return string
     */
    abstract protected function  getVariableName();

    /**
     * @return mixed
     */
    public function buildSetupOptions()
    {
        return $this->setupOptions = array();
    }

    /**
     * @return mixed
     */
    public function getSetupOptions()
    {
        return $this->setupOptions;
    }

    /**
     * @param $option
     */
    public function addSetupOption($option)
    {
        $this->setupOptions[$option->getVariableName()] = $option;
    }


}

