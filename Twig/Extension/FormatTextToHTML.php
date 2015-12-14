<?php

namespace SSone\CMSBundle\Twig\Extension;

/**
 * A simple twig extension that adds a to_array filter
 * It will convert an object to array so that you can iterate over it's properties
 */
class FormatTextToHTML extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('formatTextToHTML', array($this, 'formatTextToHTML')),
        );
    }

    public function formatParagraphsToHTML($text)
    {
        $html = "<p>";
        $text = str_replace("\r", '<br />', $text);
        $text = str_replace("\n", '</p><p>', $text);
        $html .= $text;
        $html .= "</p>";

        return $html;
    }

    public function getName()
    {
        return 'SSone_formatTextToHTML_extension';
    }
}