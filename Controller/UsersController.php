<?php

namespace JfxNinja\CMSBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use JfxNinja\CMSBundle\Entity\User;
use JfxNinja\CMSBundle\Entity\Role;

use JfxNinja\CMSBundle\Form\Type\UserTYPE;
use Doctrine\Common\Collections\ArrayCollection;



class UsersController extends Controller
{

    public function indexAction()
    {

        $users = $this->getDoctrine()
            ->getRepository('JfxNinjaCMSBundle:User')
            ->getItemsForListTable();


        return $this->render(
            'JfxNinjaCMSBundle:AdminTemplates:standardList.html.twig',
            array(
                "items" => $users,
                "title" => "Users"
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

        $roles = $this->getDoctrine()->getRepository('JfxNinjaCMSBundle:Role');

        if($mode == "new")
        {
            $user = new User();
        }
        else
        {
            $user = $this->getDoctrine()->getRepository('JfxNinjaCMSBundle:User')->findBySecurekey($securekey);
        }

        $form = $this->createForm(new UserTYPE($mode,$locale), $user);

        $form->handleRequest($request);
        if ($form->isValid())
        {

            $this->get('jfxninja.cms.recordauditor')->auditRecord($user);


            if($form['password']->getData())
            {
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword($form['password']->getData(), $user->getSalt());
                $user->setPassword($password);
            }

            switch($mode)
            {

                case "new":
                    $adminRole = $roles->getRole("ROLE_ADMIN");
                    $user->addRole($adminRole);
                    $em->persist($user);
                    break;

                case "delete":
                    $em->remove($user);
                    break;

            }

            $em->flush();

            return $this->redirect(
                $this->generateUrl('jfxninja_cms_admin_users_list')
            );
        }


        return $this->render('JfxNinjaCMSBundle:User:crud.html.twig', array(
            'form' => $form->createView(),
            'title' => $user->getUsername(),
            'mode' => $mode,
        ));



    }




}