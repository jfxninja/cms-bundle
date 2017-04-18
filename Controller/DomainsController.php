<?php

namespace JfxNinja\CMSBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use JfxNinja\CMSBundle\Entity\Domain;
use JfxNinja\CMSBundle\Form\Type\DomainTYPE;



class DomainsController extends Controller
{

    public function indexAction()
    {

        $domains = $this->getDoctrine()
            ->getRepository('JfxNinjaCMSBundle:Domain')
            ->getItemsForListTable();


        return $this->render(
            'JfxNinjaCMSBundle:AdminTemplates:standardList.html.twig',
            array(
                "items" => $domains,
                "title" => "Domains"
            )
        );

    }

    public function newAction(Request $request, $mode)
    {
        return $this->crud($request,$mode);
    }

    public function editAction(Request $request, $securekey, $mode)
    {
        return $this->crud($request,$mode,$securekey);
    }

    public function deleteAction(Request $request, $securekey, $mode)
    {
        return $this->crud($request,$mode,$securekey);
    }


    /**
     * @param Request $request
     * @param $mode
     * @param null $securekey
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    private function crud(Request $request, $mode,  $securekey = null)
    {

        $locale = $request->getLocale();
        $em = $this->getDoctrine()->getManager();

        if($mode == "new")
        {
            $domain = new Domain();
        }
        else
        {
            $domain = $this->getDoctrine()->getRepository('JfxNinjaCMSBundle:Domain')->findBySecurekey($securekey);
        }

        $form = $this->createForm(new DomainTYPE($mode,$locale), $domain);

        $form->handleRequest($request);
        if ($form->isValid())
        {

            $this->get('jfxninja.cms.recordauditor')->auditRecord($domain);

            $uploader = $this->get('jfxninja.cms.fileuploader');

            if($fp = $uploader->templateUpload($form['file_domainHTMLTemplate']->getData(), "domain"))
            {
                $domain->setDomainHTMLTemplate($fp);
            }

            switch($mode)
            {

                case "new":
                    $em->persist($domain);
                    break;

                case "delete":
                    $em->remove($domain);
                    break;

            }

            $em->flush();

            return $this->redirect(
                $this->generateUrl('jfxninja_cms_admin_domains_list')
            );
        }


        return $this->render('JfxNinjaCMSBundle:Domain:crud.html.twig', array(
            'form' => $form->createView(),
            'domainTitle' => $domain->getName(),
            'mode' => $mode,
        ));



    }




}