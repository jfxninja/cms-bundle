<?php

namespace JfxNinja\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use JfxNinja\CMSBundle\Form\Type\InstallTYPE;
use JfxNinja\CMSBundle\Entity\Domain;
use JfxNinja\CMSBundle\Entity\ContentType;
use JfxNinja\CMSBundle\Entity\Field;
use JfxNinja\CMSBundle\Entity\Content;
use JfxNinja\CMSBundle\Entity\Block;
use JfxNinja\CMSBundle\Entity\BlockField;
use JfxNinja\CMSBundle\Entity\Menu;
use JfxNinja\CMSBundle\Entity\MenuItem;
use JfxNinja\CMSBundle\Entity\Role;
use JfxNinja\CMSBundle\Entity\User;


class MaintenanceController extends Controller
{

    public function cmsInstallAction(Request $request)
    {


        $cmsMigrationService = $this->get('jfxninja.cms.migration');

        $em = $this->getDoctrine()->getManager();

        $users = $this->getDoctrine()
            ->getRepository('JfxNinjaCMSBundle:User')
            ->findAll();

        //if($users) return new RedirectResponse('/');

        $form = $this->createForm(new InstallTYPE());

        $form->handleRequest($request);


        if ($form->isValid())
        {

            $formData = $form->getData();

            $cmsMigrationService->migrate();

            $this->createAdminUser($formData,$em);

            $domain = $this->createDomain($formData,$em);

            $this->createDefaultContent($domain,$em);

            $em->flush();

            return new RedirectResponse($this->generateUrl('login'));

        }

        return $this->render('JfxNinjaCMSBundle:Maintenance:install.html.twig', array(
            'form' => $form->createView(),
        ));




    }


    private function createAdminUser($formData,$em)
    {


        $username = $formData['username'];
        $email = $formData['email'];
        $password = $formData['password'];

        //Create the default admin role
        $adminRole = new Role();
        $adminRole->setRole("ROLE_ADMIN");
        $adminRole->setName("Administrator");

        //Create the default super admin role
        $superAdminRole = new Role();
        $superAdminRole->setRole("ROLE_SUPER_ADMIN");
        $superAdminRole->setName("Super Administrator");

        //Create the default developer admin role
        $maintenanceRole = new Role();
        $maintenanceRole->setRole("ROLE_MAINTENANCE");
        $maintenanceRole->setName("Maintenance User");

        //Create the default admin user
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setCreatedBy("System");
        $user->setModifiedBy("System");
        $user->addRole($adminRole);
        $user->addRole($superAdminRole);
        $user->addRole($maintenanceRole);

        $factory = $this->container->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword($password, $user->getSalt());
        $user->setPassword($password);

        $em->persist($adminRole);
        $em->persist($superAdminRole);
        $em->persist($maintenanceRole);
        $em->persist($user);


    }

    private function createDomain($formData,$em)
    {
        $domain = new Domain();
        $domain->setDomain($formData['domain'].','.$formData['dev_domain']);
        $domain->setName('Default Domain');
        $domain->setCreatedBy('Install');
        $domain->setModifiedBy('Install');
        $em->persist($domain);

        return $domain;
    }

    private function createDefaultContent($domain, $em)
    {

        //Create contentType
        $contentType = new ContentType();
        $contentType->setCreatedBy('Install');
        $contentType->setModifiedBy('Install');
        $contentType->setHideFromMenus(false);
        $contentType->setName('Default content type');
        $contentType->setSlug(array('en'=>'default'));
        $em->persist($contentType);

        //Create Field
        $wyswigFieldType = $this->getDoctrine()->getRepository('JfxNinjaCMSBundle:FieldType')->findOneBy(array('variableName'=>'wysiwyg'));

        $wyswigField = new Field();
        $wyswigField->setName('WYSIWIG Content');
        $wyswigField->setCreatedBy('Install');
        $wyswigField->setModifiedBy('Install');
        $wyswigField->setSort(1);
        $wyswigField->setVariableName('defaultVariable');
        $wyswigField->setIsRepeatable(false);
        $wyswigField->setIsRequired(false);
        $wyswigField->setContentTypeByVariable($contentType);
        $wyswigField->setLabel('Default Label');
        $wyswigField->setFieldType($wyswigFieldType);

        $fieldTypeOptions = $this->getDoctrine()
            ->getRepository('JfxNinjaCMSBundle:FieldSetupOptions')
            ->getAllFieldSetupOptions();

        foreach($fieldTypeOptions as $fieldType)
        {

            $fieldTypeOptionsFormatted[$fieldType['fieldTypeVariableName']] = array();
            foreach($fieldType['options'] as $k=>$settings)
            {

                $value = '';
                if($settings['inputType'] == 'checkbox') $value = false;
                $fieldTypeOptionsFormatted[$fieldType['fieldTypeVariableName']][$k] = $value;
            }
        }

        $wyswigField->setFieldTypeSettings($fieldTypeOptionsFormatted);

        $em->persist($wyswigField);





        //Create content
        $content = new Content();
        $content->setCreatedBy('Install');
        $content->setModifiedBy('Install');
        $content->setName('Default Content');
        $content->setContentType($contentType);
        $content->setSlug(array('en'=>'default'));
        $em->persist($content);

        //Create Blocks
        $block = new Block();
        $block->setCreatedBy('Install');
        $block->setModifiedBy('Install');
        $block->setContent($content);
        $block->setSort(1);
        $block->setField($wyswigField);
        $em->persist($block);

        //Create BlockFields
        $blockField = new BlockField();
        $blockField->setCreatedBy('Install');
        $blockField->setModifiedBy('Install');
        $blockField->setBlock($block);
        $blockField->setFieldContent(array('defaultVariable'=>'<p>Welcome to One CMS</p>'));
        $em->persist($blockField);


        //Create Menu
        $menu = new Menu();
        $menu->setCreatedBy('Install');
        $menu->setModifiedBy('Install');
        $menu->setDomain($domain);
        $menu->setName('Default Menu');
        $menu->setSort(1);
        $menu->setMenuTemplatePosition('A');
        $menu->setDrawAllGrandChildren(true);
        $menu->setGrandChildrenTemplatePosition('A');
        $menu->setGrandChildrenRelativePosition('inline');
        $em->persist($menu);

        //Create menuitem
        $menuItem = new MenuItem();
        $menuItem->setCreatedBy('Install');
        $menuItem->setModifiedBy('Install');
        $menuItem->setContent($content);
        $menuItem->setPageClass('default');
        $menuItem->setName(array('en'=>'Default Home Menu'));
        $menuItem->setSort(1);
        $menuItem->setMode('single');
        $menuItem->setRoot($menu);
        $menuItem->setDrawAllGrandChildren(true);
        $menuItem->setGrandChildrenTemplatePosition('A');
        $menuItem->setGrandChildrenRelativePosition('inline');
        $menuItem->setHideEmptyCategories(false);
        $menuItem->setDrawListItemsAsMenuItems(false);
        $menuItem->setSlug(array('en'=>''));
        $em->persist($menuItem);

        $em->flush();


        $this->cacheContent($content->getId(),$em);

    }


    private function cacheContent($id,$em)
    {

        $cs = $this->get('jfxninja.cms.content');


        $languages = $em->getRepository('JfxNinjaCMSBundle:Language')->findAll();

        foreach($languages as $l)
        {
            $lc = $l->getLanguageCode();
            $blocks[$lc] = $cs->getBlocks("content",$id,$lc);
        }

        $content = $em->getRepository('JfxNinjaCMSBundle:Content')->find($id);

        $content->setContent($blocks);

        $em->flush();

    }


    public function cmsUpgradeAction()
    {

    }

}