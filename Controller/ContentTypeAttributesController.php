<?php

namespace JfxNinja\CMSBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use JfxNinja\CMSBundle\Entity\BlockField;
use JfxNinja\CMSBundle\Entity\Block;


use JfxNinja\CMSBundle\Form\Type\ContentTypeAttributesTYPE;


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
        $contentService = $this->get('jfxninja.cms.content');
        $blockService = $this->get('jfxninja.cms.block');
        $ls = $this->get('jfxninja.cms.Localiser');

        $altLinks = $ls->getAltAdminLangLinks($request->getUri());

        $contentType = $this->getDoctrine()->getRepository('JfxNinjaCMSBundle:ContentType')->findBySecurekey($securekey);

        //Add/Remove blocks as necessary
        $blockService->contentTypeBlockManager($contentType);


        $form = $this->createForm(new ContentTypeAttributesTYPE($mode,$fieldsRepository,$contentService,$locale), $contentType);


        $form->handleRequest($request);
        if ($form->isValid())
        {
            $auditor = $this->get('jfxninja.cms.recordauditor');
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
                $this->generateUrl('jfxninja_cms_admin_content_list')
            );
        }


        return $this->render('JfxNinjaCMSBundle:Content:crud.html.twig', array(
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
            ->getRepository('JfxNinjaCMSBundle:Field');
    }


}