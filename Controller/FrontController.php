<?php

namespace SSone\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class FrontController extends Controller
{

    public $redirect;

    /**
     * @param Request $request
     * @param $uri
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, $uri)
    {

        if ($this->has('debug.stopwatch')) {
        $stopwatch = $this->get('debug.stopwatch');
        }
        else
        {
            $stopwatch = false;
        }


        $navService = $this->get('ssone.cms.navigation');
        $contentService = $this->get('ssone.cms.content');
        $moduleService = $this->get('ssone.cms.module');
        $blockService = $this->get('ssone.cms.block');
        $CMSFormService = $this->get('ssone.cms.form');

        $localiser = $this->get('ssone.cms.localiser');
        $em = $this->getDoctrine()->getManager();

        //2)Resolve navigation request
        $navService->handleURL($request->getHost(),$uri,$stopwatch);
        $activeMenuItem = $navService->activeMenuItem;

        //4) Retrive URL Content
        $content = $contentService->retrieveMenuContent($activeMenuItem,$uri);

        //Retrieve modular content
        $modules = $moduleService->getURLModules($uri,$this);

        $content = $CMSFormService->processForms($content,$this,$uri);

        //5) Build alternate language urls
        $slug = isset($content['attributes']['slug']) ? $content['attributes']['slug'] : "";
        $mlLinks = $navService->getAlternateLanguageURIs($slug);

        if($this->redirect)
        {
            $routeParams = array('uri'=>$this->redirect);
            if(preg_match("/\?/",$this->redirect))
            {
                $splitUrl = explode("?",$this->redirect);
                $uri = array('uri'=>$uri);
                parse_str($splitUrl[1],$params);
                $routeParams = array_merge($uri,$params);
            }

            if($localiser->locale == $localiser->defaultLocale)
            {

                return $this->redirect(
                    $this->generateUrl('ssone_cms_frontend_noloco',$routeParams)
                );
            }
            else
            {
                return $this->redirect(
                    $this->generateUrl('ssone_cms_frontend',$routeParams)
                );
            }

        }



        if ( $this->get('templating')->exists($navService->domainTemplate) )
        {
            $domainTemplate = $navService->domainTemplate;
        }
        elseif($navService->domainTemplate)
        {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Template: '. $navService->domainTemplate . ' was not found'
            );

            $domainTemplate = 'SSoneCMSBundle:Default:domain.html.twig';
        }
        else
        {
            $domainTemplate = 'SSoneCMSBundle:Default:domain.html.twig';
        }

        return $this->render(
            $domainTemplate,
            array(
                'navigation'=>$navService->templateNavigationMap,
                'pageClass'=>$navService->pageClass,
                'pageTitle'=>$navService->pageTitle,
                'metaDescription'=>$navService->metaDescription,
                'content'=>$content,
                'modules'=>$modules,
                'multiLanguageLinks'=>$mlLinks
            )

        );

    }





}

