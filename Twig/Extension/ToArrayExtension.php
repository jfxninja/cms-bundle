<?php

namespace JfxNinja\CMSBundle\Twig\Extension;

/**
 * A simple twig extension that adds a to_array filter
 * It will convert an object to array so that you can iterate over it's properties
 */
class ToArrayExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('to_array', array($this, 'to_array')),
        );
    }

    public function to_array($object)
    {
        return get_object_vars($object);
    }

    public function getName()
    {
        return 'to_array_extension';
    }
}