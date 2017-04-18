<?php

namespace JfxNinja\CMSBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use JfxNinja\CMSBundle\Entity\Module;
use JfxNinja\CMSBundle\Form\Type\ModuleTYPE;




class ModulesController extends Controller
{

    public function indexAction()
    {

        $ms = $this->get('jfxninja.cms.module');

        $menuItems = $ms->getItemsForListTable();


        return $this->render(
            'JfxNinjaCMSBundle:AdminTemplates:standardList.html.twig',
            array(
                "items" => $menuItems,
                "title" => "Modules"
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
        $defaultLocale = $this->container->getParameter("jfxninja.default_locale");
        $em = $this->getDoctrine()->getManager();

        $ls = $this->get('jfxninja.cms.Localiser');
        $altLinks = $ls->getAltAdminLangLinks($request->getUri());


        if($mode == "new")
        {
            $module = new Module();
        }
        else
        {
            $module = $this->getDoctrine()->getRepository('JfxNinjaCMSBundle:Module')->findBySecurekey($securekey);
        }


        $contentService = $this->get('jfxninja.cms.content');

        $form = $this->createForm(new ModuleTYPE($em,$contentService,$locale),$module);

        $form->handleRequest($request);
        if ($form->isValid())
        {

            $this->get('jfxninja.cms.recordauditor')->auditRecord($module);

            $uploader = $this->get('jfxninja.cms.fileuploader');

            if($form['file_templatePath']->getData() && $fp = $uploader->templateUpload($form['file_templatePath']->getData(), "module"))
            {
                $module->setTemplatePath($fp);
            }

            switch($mode)
            {

                case "new":
                    $em->persist($module);
                    break;

                case "delete":
                    $em->remove($module);
                    break;

            }

            $em->flush();

            return $this->redirect(
                $this->generateUrl('jfxninja_cms_admin_modules_list')
            );
        }

        return $this->render('JfxNinjaCMSBundle:Module:crud.html.twig', array(
            'form' => $form->createView(),
            'menuItemTitle' => $module->getName($defaultLocale),
            'mode' => $mode,
            'locale' => $locale,
            'altLinks' => $altLinks,
        ));


    }




}