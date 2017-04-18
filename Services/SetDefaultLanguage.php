<?php

namespace JfxNinja\CMSBundle\Services;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Doctrine\ORM\EntityManager;

class SetDefaultLanguage
{
    protected $container;
    protected $em;

    public function __construct(EntityManager $em,ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $em;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernel::MASTER_REQUEST != $event->getRequestType()) {
            // don't do anything if it's not the master request
            return;
        }
        $request = $event->getRequest();
        $locale = $request->attributes->get("_locale");
        if($locale == "default")
        {
            $request->attributes->set('_locale', "en");
        }


    }
}