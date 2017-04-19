<?php

namespace JfxNinja\CMSBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use JfxNinja\CMSBundle\Entity\MenuItem;
use JfxNinja\CMSBundle\Entity\Menu;
use JfxNinja\CMSBundle\Entity\Content;

use JfxNinja\CMSBundle\Form\Type\MenuItemTYPE;
use Doctrine\Common\Collections\ArrayCollection;



class MenuItemsController extends Controller
{

    public function indexAction()
    {

        $menuItems = $this->getDoctrine()
            ->getRepository('JfxNinjaCMSBundle:MenuItem')
            ->getItemsForListTable();


        return $this->render(
            'JfxNinjaCMSBundle:AdminTemplates:standardList.html.twig',
            array(
                "items" => $menuItems,
                "title" => "Menu Items"
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
            $menuItem = new MenuItem();
        }
        else
        {
            $menuItem = $this->getDoctrine()->getRepository('JfxNinjaCMSBundle:MenuItem')->findBySecurekey($securekey);
        }


        $contentLibrary = $this->get('jfxninja.cms.content')->getAllForGroupedChoiceOptions();

        $fieldsRepository = $this->getDoctrine()->getRepository('JfxNinjaCMSBundle:Field');

        $menuItems = $this->get('jfxninja.cms.navigation');

        $menuItems = $menuItems->buildFlatNavigationArray($menuItem->getId());

        $form = $this->createForm(new MenuItemTYPE($request->getLocale(),$mode,$contentLibrary,$menuItems,$fieldsRepository),$menuItem);

        $form->handleRequest($request);
        if ($form->isValid())
        {


            $this->handleParentChoice($menuItem,$em);
            $this->handleContentChoice($menuItem,$em);
            $this->get('jfxninja.cms.recordauditor')->auditRecord($menuItem);

            switch($mode)
            {

                case "new":
                    $em->persist($menuItem);
                    break;

                case "delete":
                    $em->remove($menuItem);
                    break;

            }

            $em->flush();

            return $this->redirect(
                $this->generateUrl('jfxninja_cms_admin_menuItems_list')
            );
        }

        return $this->render('JfxNinjaCMSBundle:MenuItem:crud.html.twig', array(
            'form' => $form->createView(),
            'menuItemTitle' => $menuItem->getName($defaultLocale),
            'mode' => $mode,
            'locale' => $locale,
            "altLinks" => $altLinks,
        ));


    }

    private function handleParentChoice($menuItem,$em)
    {

            $parent = explode("_",$menuItem->getMapAttached());

            //If a parent has been chosen and its not set to root set the parent menu
            if(isset($parent[1]) && $parent[1] != "root")
            {
                $menuItem->setParent($em->getReference('JfxNinja\CMSBundle\Entity\MenuItem', $parent[1]));
            }
            elseif(isset($parent[1]) && $parent[1] == "root")
            {
                $menuItem->setParent();
            }

            //set the root attachment
            //TODO:JW test this should always be set
            if($parent[0])
            {
                $menuItem->setRoot($em->getReference('JfxNinja\CMSBundle\Entity\Menu', $parent[0]));
            }
    }


    //need to integrate and write this function
    private function handleContentChoice($menuItem,$em)
    {
        /////////////////////////////////
        //Handle content choice
        $content = explode("_",$menuItem->getMapContent());
        if($content[0] == "single")
        {
            $menuItem->setContent($em->getReference('JfxNinja\CMSBundle\Entity\Content', $content[1]));
            $menuItem->setMode('single');
        }
        elseif($content[0] == "list")
        {
            $menuItem->setContentType($em->getReference('JfxNinja\CMSBundle\Entity\ContentType', $content[1]));
            $menuItem->setMode('list');
        }
        elseif(count($content)<2)
        {
            $menuItem->setContentType();
            $menuItem->setContent();
            $menuItem->setMode('place-holder');
        }
    }




}