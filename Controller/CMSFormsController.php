<?php

namespace SSone\CMSBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use SSone\CMSBundle\Entity\CMSForm;

use SSone\CMSBundle\Form\Type\CMSFormTYPE;
use Doctrine\Common\Collections\ArrayCollection;



class CMSFormsController extends Controller
{

    public function indexAction()
    {

        $CMSForms = $this->getDoctrine()
            ->getRepository('SSoneCMSBundle:CMSForm')
            ->getItemsForListTable();


        return $this->render(
            'SSoneCMSBundle:AdminTemplates:standardList.html.twig',
            array(
                "items" => $CMSForms,
                "title" => "Forms"
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
            $CMSForm = new CMSForm();
        }
        else
        {
            $CMSForm = $this->getDoctrine()->getRepository('SSoneCMSBundle:CMSForm')->findBySecurekey($securekey);
        }

        $form = $this->createForm(new CMSFormTYPE($mode,$locale), $CMSForm);

        $form->handleRequest($request);
        if ($form->isValid())
        {

            $this->get('ssone.cms.recordauditor')->auditRecord($CMSForm);

            $uploader = $this->get('ssone.cms.fileuploader');

            if($fp = $uploader->templateUpload($form['file_template']->getData(), "form"))
            {
                $CMSForm->setTemplate($fp);
            }

            switch($mode)
            {

                case "new":
                    $em->persist($CMSForm);
                    break;

                case "delete":
                    $em->remove($CMSForm);
                    break;

            }

            $em->flush();

            return $this->redirect(
                $this->generateUrl('ssone_cms_admin_cmsforms_list')
            );
        }


        return $this->render('SSoneCMSBundle:CMSForm:crud.html.twig', array(
            'form' => $form->createView(),
            'formTitle' => $CMSForm->getName(),
            'mode' => $mode,
        ));



    }




}