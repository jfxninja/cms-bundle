<?php

namespace JfxNinja\CMSBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class AdminNavigationController extends Controller
{
    public function drawAdminNavigationAction($route)
    {

        $menus = array(

            array(
                "name"=>"Content",
                "route"=>"jfxninja_cms_admin_content_list_default",
                "permission"=>"ROLE_ADMIN"
            ),
            array(
                "name"=>"Content Types",
                "route"=>"jfxninja_cms_admin_contentTypes_list",
                "permission"=>"ROLE_SUPER_ADMIN"
            ),
            array(
                "name"=>"Menu Items",
                "route"=>"jfxninja_cms_admin_menuItems_list",
                "permission"=>"ROLE_ADMIN"
            ),
            array(
                "name"=>"Menus",
                "route"=>"jfxninja_cms_admin_menus_list",
                "permission"=>"ROLE_SUPER_ADMIN"
            ),

            array(
                "name"=>"Modules",
                "route"=>"jfxninja_cms_admin_modules_list",
                "permission"=>"ROLE_SUPER_ADMIN"
            ),
            array(
                "name"=>"Forms",
                "route"=>"jfxninja_cms_admin_cmsforms_list",
                "permission"=>"ROLE_ADMIN"
            ),
            array(
                "name"=>"Domains",
                "route"=>"jfxninja_cms_admin_domains_list",
                "permission"=>"ROLE_SUPER_ADMIN"
            ),
            array(
                "name"=>"Users",
                "route"=>"jfxninja_cms_admin_users_list",
                "permission"=>"ROLE_ADMIN"
            ),
            array(
                "name"=>"Languages",
                "route"=>"jfxninja_cms_admin_languages_list",
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
            $en = str_replace("JfxNinja\\CMSBundle\\Entity\\", '', $e);
            $en = str_replace("JfxNinja\\CMSBundle\\Entity\\", '', $en);
            $emenu[] = str_replace("JfxNinja\\CMSBundle\\Entity\\", '', $en);
        }

        return $this->render(
            'JfxNinjaCMSBundle:AdminTemplates:mainNavigation.html.twig',
            array("menus" => array('mmenus' => $menus, "emenus" => $emenu))
        );
    }

}