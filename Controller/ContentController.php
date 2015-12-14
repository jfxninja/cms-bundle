<?php

namespace SSone\CMSBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use SSone\CMSBundle\Entity\Content;

use SSone\CMSBundle\Form\Type\ContentTYPE;




class ContentController extends Controller
{

    public function indexAction(Request $request, $securekey)
    {

        $ctSecurekey = $securekey;

        $em = $this->getDoctrine()->getManager();

        $content = $this->get('ssone.cms.content')->getItemsForListTable($ctSecurekey);

        $contentType = $em->getRepository('SSoneCMSBundle:ContentType')->getContentListHeading($ctSecurekey);

        $subTitle = ($contentType) ? $contentType->getName() : 'No content type defined';

        //get content submenus
        $smenus = $this->getDoctrine()->getRepository('SSoneCMSBundle:ContentType')->buildContentTypeMenu();


        return $this->render(
            'SSoneCMSBundle:Content:contentList.html.twig',
            array(
                "items" => $content,
                "menus" => $smenus,
                "subTitle" => $subTitle
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

        $ls = $this->get('ssone.cms.Localiser');
        $em = $this->getDoctrine()->getManager();
        $cs = $this->get('ssone.cms.content');
        $fieldsRepository = $this->getFieldsRepository();
        $blockService = $this->get('ssone.cms.block');
        $CMSFormService = $this->get('ssone.cms.form');

        $altLinks = $ls->getAltAdminLangLinks($request->getUri());

        if($mode == "new")
        {
            $content = new content();
        }
        else
        {

            $content = $cs->findBySecurekey($securekey);

            //Add/Remove blocks as necessary
            $blockService->contentBlockManager($content);

        }

        $form = $this->createForm(new ContentTYPE($mode,$fieldsRepository,$cs,$CMSFormService,$locale), $content);


        $form->handleRequest($request);
        if ($form->isValid())
        {
            $auditor = $this->get('ssone.cms.recordauditor');
            $auditor->auditRecord($content);

            switch($mode)
            {
                case "edit":

                    //Audit blocks and fields
                    foreach($content->getBlocks() as $block)
                    {
                        $auditor->auditRecord($block);

                        foreach($block->getBlockFields() as $blockField)
                        {

                            $auditor->auditRecord($blockField);

                        }

                    }

                    $blockService->handleUploadBlocks($form);

                    $blockService->handleRemovedBlockFields($content->getId(),$content->getBlocks());

                    $em->flush();
                    $this->cacheContent($content->getId(),$cs,$em);

                    break;

                case "new":
                    $em->persist($content);
                    $em->flush();
                    $storedContent = $cs->findSecureKeyById($content->getId());
                    $this->cacheContent($content->getId(),$cs,$em);
                    return $this->redirect(
                        $this->generateUrl('ssone_cms_admin_content_edit',array('securekey' => $storedContent['securekey']))
                    );
                    break;

                case "delete":
                    $em->remove($content);
                    $em->flush();
                    break;

            }



            return $this->redirect(
                $this->generateUrl('ssone_cms_admin_content_list', array('securekey' => $content->getContentType()->getSecurekey()))
            );
        }


        return $this->render('SSoneCMSBundle:Content:crud.html.twig', array(
            'form' => $form->createView(),
            'contentTitle' => $content->getName(),
            'mode' => $mode,
            'locale' => $locale,
            "altLinks" => $altLinks,
        ));



    }



    private function cacheContent($id,$cs,$em)
    {

        $languages = $em->getRepository('SSoneCMSBundle:Language')->findAll();

        foreach($languages as $l)
        {
            $lc = $l->getLanguageCode();
            $blocks[$lc] = $cs->getBlocks("content",$id,$lc);
        }

        $content = $em->getRepository('SSoneCMSBundle:Content')->find($id);

        $content->setContent($blocks);

        $em->flush();

    }


    private function getFieldsRepository()
    {
        return $this->getDoctrine()
            ->getRepository('SSoneCMSBundle:Field');
    }


}