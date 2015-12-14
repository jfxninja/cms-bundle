<?php

namespace SSone\CMSBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use SSone\CMSBundle\Entity\BlockField;
use SSone\CMSBundle\Entity\Block;


use SSone\CMSBundle\Form\Type\ContentTypeAttributesTYPE;


class ContentTypeAttributesController extends Controller
{


    /**
     * @param Request $request
     * @param $mode
     * @param null $securekey
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $mode,  $securekey = null)
    {

        $locale = $request->getLocale();
        $em = $this->getDoctrine()->getManager();
        $fieldsRepository = $this->getFieldsRepository();
        $contentService = $this->get('ssone.cms.content');
        $blockService = $this->get('ssone.cms.block');
        $ls = $this->get('ssone.cms.Localiser');

        $altLinks = $ls->getAltAdminLangLinks($request->getUri());

        $contentType = $this->getDoctrine()->getRepository('SSoneCMSBundle:ContentType')->findBySecurekey($securekey);

        //Add/Remove blocks as necessary
        $blockService->contentTypeBlockManager($contentType);


        $form = $this->createForm(new ContentTypeAttributesTYPE($mode,$fieldsRepository,$contentService,$locale), $contentType);


        $form->handleRequest($request);
        if ($form->isValid())
        {
            $auditor = $this->get('ssone.cms.recordauditor');
            $auditor->auditRecord($contentType);


            //Audit blocks and fields
            foreach($contentType->getBlocks() as $block)
            {
                $auditor->auditRecord($block);

                foreach($block->getBlockFields() as $blockField)
                {

                    $auditor->auditRecord($blockField);

                }

            }
            $blockService->handleUploadBlocks($form);

            $em->flush();

            return $this->redirect(
                $this->generateUrl('ssone_cms_admin_content_list')
            );
        }


        return $this->render('SSoneCMSBundle:Content:crud.html.twig', array(
            'form' => $form->createView(),
            'contentTitle' => $contentType->getName(),
            'mode' => $mode,
            'locale' => $locale,
            "altLinks" => $altLinks,
        ));



    }



    private function getFieldsRepository()
    {
        return $this->getDoctrine()
            ->getRepository('SSoneCMSBundle:Field');
    }


}