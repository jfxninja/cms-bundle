<?php

namespace JfxNinja\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('JfxNinjaCMSBundle:Default:index.html.twig', array());
    }
}
