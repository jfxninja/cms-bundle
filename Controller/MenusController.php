<?php

namespace JfxNinja\CMSBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use JfxNinja\CMSBundle\Entity\Menu;

use JfxNinja\CMSBundle\Form\Type\MenuTYPE;
use Doctrine\Common\Collections\ArrayCollection;



class MenusController extends Controller
{

    public function indexAction()
    {

        $menus = $this->getDoctrine()
            ->getRepository('JfxNinjaCMSBundle:Menu')
            ->getItemsForListTable();


        return $this->render(
            'JfxNinjaCMSBundle:AdminTemplates:standardList.html.twig',
            array(
                "items" => $menus,
                "title" => "Site Menus"
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
            $menu = new Menu();
        }
        else
        {
            $menu = $this->getDoctrine()->getRepository('JfxNinjaCMSBundle:Menu')->findBySecurekey($securekey);
        }

        $form = $this->createForm(new MenuTYPE($mode), $menu);

        $form->handleRequest($request);
        if ($form->isValid())
        {

            $this->get('jfxninja.cms.recordauditor')->auditRecord($menu);

            $uploader = $this->get('jfxninja.cms.fileuploader');

            if($form['file_menuTemplate']->getData() && $fp = $uploader->templateUpload($form['file_menuTemplate']->getData(), "menu"))
            {
                $menu->setMenuTemplate($fp);
            }

            switch($mode)
            {

                case "new":
                    $em->persist($menu);
                    break;

                case "delete":
                    $em->remove($menu);
                    break;

            }

            $em->flush();

            return $this->redirect(
                $this->generateUrl('jfxninja_cms_admin_menus_list')
            );
        }

        return $this->render('JfxNinjaCMSBundle:Menu:crud.html.twig', array(
            'form' => $form->createView(),
            'menuTitle' => $menu->getName(),
            'mode' => $mode,
        ));

    }

}