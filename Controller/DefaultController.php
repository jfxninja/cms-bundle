<?php

namespace SSone\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SSoneCMSBundle:Default:index.html.twig', array());
    }
}
