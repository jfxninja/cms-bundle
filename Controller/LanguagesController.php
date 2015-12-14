<?php

namespace SSone\CMSBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use SSone\CMSBundle\Entity\Language;
use SSone\CMSBundle\Form\Type\LanguageTYPE;



class LanguagesController extends Controller
{

    public function indexAction()
    {

        $languages = $this->getDoctrine()
            ->getRepository('SSoneCMSBundle:Language')
            ->getItemsForListTable();


        return $this->render(
            'SSoneCMSBundle:AdminTemplates:standardList.html.twig',
            array(
                "items" => $languages,
                "title" => "Languages"
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
            $language = new Language();
        }
        else
        {
            $language = $this->getDoctrine()->getRepository('SSoneCMSBundle:Language')->findBySecurekey($securekey);
        }

        $form = $this->createForm(new LanguageTYPE($mode,$locale), $language);

        $form->handleRequest($request);
        if ($form->isValid())
        {

            $this->get('ssone.cms.recordauditor')->auditRecord($language);


            switch($mode)
            {

                case "new":
                    $em->persist($language);
                    break;

                case "delete":
                    $em->remove($language);
                    break;

            }

            $em->flush();

            return $this->redirect(
                $this->generateUrl('ssone_cms_admin_languages_list')
            );
        }


        return $this->render('SSoneCMSBundle:Language:crud.html.twig', array(
            'form' => $form->createView(),
            'title' => $language->getName(),
            'mode' => $mode,
        ));



    }




}