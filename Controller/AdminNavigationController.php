<?php

namespace SSone\CMSBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class AdminNavigationController extends Controller
{
    public function drawAdminNavigationAction($route)
    {

        $menus = array(

            array(
                "name"=>"Content",
                "route"=>"ssone_cms_admin_content_list_default",
                "permission"=>"ROLE_ADMIN"
            ),
            array(
                "name"=>"Content Types",
                "route"=>"ssone_cms_admin_contentTypes_list",
                "permission"=>"ROLE_SUPER_ADMIN"
            ),
            array(
                "name"=>"Menu Items",
                "route"=>"ssone_cms_admin_menuItems_list",
                "permission"=>"ROLE_ADMIN"
            ),
            array(
                "name"=>"Menus",
                "route"=>"ssone_cms_admin_menus_list",
                "permission"=>"ROLE_SUPER_ADMIN"
            ),

            array(
                "name"=>"Modules",
                "route"=>"ssone_cms_admin_modules_list",
                "permission"=>"ROLE_SUPER_ADMIN"
            ),
            array(
                "name"=>"Forms",
                "route"=>"ssone_cms_admin_cmsforms_list",
                "permission"=>"ROLE_ADMIN"
            ),
            array(
                "name"=>"Domains",
                "route"=>"ssone_cms_admin_domains_list",
                "permission"=>"ROLE_SUPER_ADMIN"
            ),
            array(
                "name"=>"Users",
                "route"=>"ssone_cms_admin_users_list",
                "permission"=>"ROLE_ADMIN"
            ),
            array(
                "name"=>"Languages",
                "route"=>"ssone_cms_admin_languages_list",
                "permission"=>"ROLE_ADMIN"
            ),
            array(
                "name"=>"Logout",
                "route"=>"logout",
                "permission"=>"ROLE_ADMIN"
            ),

                    );


        $em = $this->getDoctrine()->getManager();

        $smenus = array();

        //entities menu
        $entities = $em->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();

        $emenu = array();

        foreach($entities as $e)
        {
            $en = str_replace("SSone\\CMSBundle\\Entity\\", '', $e);
            $en = str_replace("SSone\\CMSBundle\\Entity\\", '', $en);
            $emenu[] = str_replace("SSone\\CMSBundle\\Entity\\", '', $en);
        }

        return $this->render(
            'SSoneCMSBundle:AdminTemplates:mainNavigation.html.twig',
            array("menus" => array('mmenus' => $menus, "emenus" => $emenu))
        );
    }

}