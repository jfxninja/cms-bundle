<?php

namespace JfxNinja\CMSBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use JfxNinja\CMSBundle\Entity\Content;

use JfxNinja\CMSBundle\Form\Type\ContentTYPE;




class ContentController extends Controller
{

    public function indexAction(Request $request, $securekey)
    {

        $ctSecurekey = $securekey;

        $em = $this->getDoctrine()->getManager();

        $content = $this->get('jfxninja.cms.content')->getItemsForListTable($ctSecurekey);

        $contentType = $em->getRepository('JfxNinjaCMSBundle:ContentType')->getContentListHeading($ctSecurekey);

        $subTitle = ($contentType) ? $contentType->getName() : 'No content type defined';

        //get content submenus
        $smenus = $this->getDoctrine()->getRepository('JfxNinjaCMSBundle:ContentType')->buildContentTypeMenu();


        return $this->render(
            'JfxNinjaCMSBundle:Content:contentList.html.twig',
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

        $ls = $this->get('jfxninja.cms.Localiser');
        $em = $this->getDoctrine()->getManager();
        $cs = $this->get('jfxninja.cms.content');
        $fieldsRepository = $this->getFieldsRepository();
        $blockService = $this->get('jfxninja.cms.block');
        $CMSFormService = $this->get('jfxninja.cms.form');

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

        $cmsInputTypeService    = $this->get('jfxninja.cms.input_type');

        $form = $this->createForm(new ContentTYPE($mode,$cmsInputTypeService,$fieldsRepository,$cs,$CMSFormService,$locale), $content);


        $form->handleRequest($request);
        if ($form->isValid())
        {
            $auditor = $this->get('jfxninja.cms.recordauditor');
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
                        $this->generateUrl('jfxninja_cms_admin_content_edit',array('securekey' => $storedContent['securekey']))
                    );
                    break;

                case "delete":
                    $em->remove($content);
                    $em->flush();
                    break;

            }



            return $this->redirect(
                $this->generateUrl('jfxninja_cms_admin_content_list', array('securekey' => $content->getContentType()->getSecurekey()))
            );
        }


        return $this->render('JfxNinjaCMSBundle:Content:crud.html.twig', array(
            'form' => $form->createView(),
            'contentTitle' => $content->getName(),
            'mode' => $mode,
            'locale' => $locale,
            "altLinks" => $altLinks,
        ));



    }



    private function cacheContent($id,$cs,$em)
    {

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


    private function getFieldsRepository()
    {
        return $this->getDoctrine()
            ->getRepository('JfxNinjaCMSBundle:Field');
    }


}