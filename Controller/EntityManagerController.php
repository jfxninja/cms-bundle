<?php

namespace SSone\CMSBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;





class EntityManagerController extends Controller
{
    public function crudAction(Request $request,$entity,$mode,$sid)
    {
        if($mode=="list")
        {
            $items = $this->getDoctrine()
                ->getRepository('SSoneCMSBundle:'.$entity)
                ->getItemsForListTable();

            return $this->render(
                'SSoneCMSBundle:AdminTemplates:list.html.twig',
                array("items" => $items)
            );

        }

        $em = $this->getDoctrine()->getManager();
        $usr= $this->get('security.context')->getToken()->getUser();
        $formPath = "SSone\\CMSBundle\\Form\\Type\\".$entity."TYPE";
        $entityPath = "SSone\\CMSBundle\\Entity\\".$entity;
        $entityObj = new $entityPath();

        if($mode=="new")
        {
            $form = $this->createForm(new $formPath($mode,$em), $entityObj);

            $form->handleRequest($request);

            if ($form->isValid())
            {
                $entityObj->setCreatedBy($usr->getUsername());
                $entityObj->setModifiedBy($usr->getUsername());

                $em->persist($entityObj);
                $em->flush();
                return $this->redirect($this->generateUrl('ssone_cms_admin_entity_manager', array('entity'=>$entity)));
            }

            return $this->render('SSoneCMSBundle:AdminTemplates:crud.html.twig', array(
                'form' => $form->createView(),
                'title' => $entity
            ));

        }

        $entityObj = $this->getDoctrine()
            ->getRepository('SSoneCMSBundle:'.$entity)
            ->findBySecurekey($sid);

        if($mode == "edit")
        {
            $form = $this->createForm(new $formPath($mode,$em), $entityObj);
            $form->handleRequest($request);

            if ($form->isValid())
            {
                $entityObj->setModifiedBy($usr->getUsername());
                $em->flush();
                return $this->redirect($this->generateUrl('ssone_cms_admin_entity_manager', array('entity'=>$entity)));
            }


        }

        if($mode == "delete")
        {
            $form = $this->createFormBuilder($entityObj)
                ->add('delete', 'submit')
                ->getForm();

            $form->handleRequest($request);

            if ($form->isValid())
            {
                $em->remove($entityObj);
                $em->flush();
                return $this->redirect($this->generateUrl('ssone_cms_admin_entity_manager', array('entity'=>$entity)));
            }


        }

        return $this->render('SSoneCMSBundle:AdminTemplates:crud.html.twig', array(
            'form' => $form->createView(),
            'title' => $entity
        ));

    }



}