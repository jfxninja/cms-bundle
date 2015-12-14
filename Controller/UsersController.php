<?php

namespace SSone\CMSBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use SSone\CMSBundle\Entity\User;
use SSone\CMSBundle\Entity\Role;

use SSone\CMSBundle\Form\Type\UserTYPE;
use Doctrine\Common\Collections\ArrayCollection;



class UsersController extends Controller
{

    public function indexAction()
    {

        $users = $this->getDoctrine()
            ->getRepository('SSoneCMSBundle:User')
            ->getItemsForListTable();


        return $this->render(
            'SSoneCMSBundle:AdminTemplates:standardList.html.twig',
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

        $roles = $this->getDoctrine()->getRepository('SSoneCMSBundle:Role');

        if($mode == "new")
        {
            $user = new User();
        }
        else
        {
            $user = $this->getDoctrine()->getRepository('SSoneCMSBundle:User')->findBySecurekey($securekey);
        }

        $form = $this->createForm(new UserTYPE($mode,$locale), $user);

        $form->handleRequest($request);
        if ($form->isValid())
        {

            $this->get('ssone.cms.recordauditor')->auditRecord($user);


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
                $this->generateUrl('ssone_cms_admin_users_list')
            );
        }


        return $this->render('SSoneCMSBundle:User:crud.html.twig', array(
            'form' => $form->createView(),
            'title' => $user->getUsername(),
            'mode' => $mode,
        ));



    }




}