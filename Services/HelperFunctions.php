<?php

namespace JfxNinja\CMSBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class HelperFunctions
{



    public function __construct()
    {

    }


    public function urlSafe($unsafe)
    {
        $strip = array(" ", "~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
            "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
            "â€”", "â€“", ",", "<", ".", ">", "/", "?");
        $clean = str_replace($strip, "-", $unsafe);

        return $clean;

    }

    public function deserializeArray($item,$keys)
    {
        foreach($keys as $k)
        {
            if($this->is_serialized($item[$k])) $item[$k] = unserialize($item[$k]);
        }

        return $item;
    }


    public function is_serialized( $data ) {
        // if it isn't a string, it isn't serialized
        if ( !is_string( $data ) )
            return false;
        $data = trim( $data );
        if ( 'N;' == $data )
            return true;
        if ( !preg_match( '/^([adObis]):/', $data, $badions ) )
            return false;
        switch ( $badions[1] ) {
            case 'a' :
            case 'O' :
            case 's' :
                if ( preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data ) )
                    return true;
                break;
            case 'b' :
            case 'i' :
            case 'd' :
                if ( preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data ) )
                    return true;
                break;
        }
        return false;
    }


}



