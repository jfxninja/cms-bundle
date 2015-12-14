<?php

namespace SSone\CMSBundle\Twig\Extension;

/**
 * A simple twig extension that adds a to_array filter
 * It will convert an object to array so that you can iterate over it's properties
 */
class CrToBr extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('crtobr', array($this, 'crtobr')),
        );
    }

    public function crtobr($text)
    {
        $text = str_replace("\r", '<br />', $text);
        return $text;
    }

    public function getName()
    {
        return 'SSone_CrToBr_extension';
    }
}