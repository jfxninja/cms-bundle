<?php

namespace SSone\CMSBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

class Localiser
{

    public $defaultLocale;
    public $locale;
    protected $container;
    protected $em;


    public function __construct(ContainerInterface $container, Request $request,EntityManager $em)
    {
        $this->container = $container;
        $this->em = $em;
        $this->locale = $request->getLocale();
        $this->defaultLocale = $this->container->getParameter("SSone.default_locale");
    }


    public function setMultiLanguageFields($item,$fields,$locale)
    {

        foreach($fields as $f)
        {

            if(isset($item[$f][$locale]) && $item[$f][$locale])
            {
                $item[$f] = $item[$f][$locale];
            }
            elseif(isset($item[$f][$this->defaultLocale]))
            {
                $item[$f] = $item[$f][$this->defaultLocale];
            }
            else
            {
                $item[$f] = "";
            }
        }

        return $item;

    }

    public function translateMultiLanguageField(&$item,$locale)
    {

            if(isset($item[$locale]) && $item[$locale])
            {
                $t = $item[$locale];
            }
            elseif(isset($item[$this->defaultLocale]))
            {
                $t = $item[$this->defaultLocale];
            }
            else
            {
                $t = "";
            }


        return $t;

    }

    public function getAltAdminLangLinks($uri)
    {

        $links = array();

        $languages = $this->em
            ->createQuery(
                'SELECT l.languageCode, l.name
                FROM SSoneCMSBundle:Language l'
            )
            ->getResult();

        $uri = preg_replace('/\/[a-z]{2}$/', "", $uri);

        foreach($languages as $l)
        {
            $current = false;
            if($l['languageCode'] == $this->locale) $current = true;
            $links[$l['languageCode']] =
                array(
                    'link'=>$uri."/".$l['languageCode'],
                    'default'=>$current,
                    'name'=>$l['name'],
                    'code'=>$l['languageCode']
                );
        }

        return $links;

    }


}



