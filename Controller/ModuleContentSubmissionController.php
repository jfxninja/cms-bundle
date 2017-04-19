<?php

namespace JfxNinja\CMSBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use JfxNinja\CMSBundle\Entity\Content;

use JfxNinja\CMSBundle\Form\Type\ContentTYPE;


class ModuleContentSubmissionController extends Controller
{




    /**
     * @param Request $request
     * @param Request $securekey
     * @param Request $redirect
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function submitAction(Request $request, $securekey, $redirect)
    {

        $locale = $request->getLocale();
        $mode = "module-submission";
        $em = $this->getDoctrine()->getManager();
        $cs = $this->get('jfxninja.cms.content');
        $fieldsRepository = $this->getFieldsRepository();
        $blockService = $this->get('jfxninja.cms.block');

        $contentType = $this->getDoctrine()->getRepository('JfxNinjaCMSBundle:ContentType')->findBySecurekey($securekey);

        $content = new content();

        $content->setContentType($contentType);


        $blockService->contentBlockManager($content);

        $form = $this->createForm(new ContentTYPE($mode,$fieldsRepository,$cs,$locale), $content);




        $form->handleRequest($request);
        print($contentType->getName());
        print($form->isValid());
        if ($form->isValid())
        {


            $auditor = $this->get('jfxninja.cms.recordauditor');
            $auditor->auditRecord($content);


                    //Audit blocks and fields
                    foreach($content->getBlocks() as $block)
                    {
                        $auditor->auditRecord($block);

                        foreach($block->getBlockFields() as $blockField)
                        {

                            $auditor->auditRecord($blockField);

                        }

                    }

                    //handle file uploads
                    $uploader = $this->get('jfxninja.cms.fileuploader');

                    foreach ($form['blocks'] as $block) {

                        foreach($block['blockFields'] as $blockField)
                        {

                            foreach($blockField['fieldContent'] as $input)
                            {

                                if(strpos($input->getName(),'_fileupload') !== false)
                                {

                                    $params = explode("_",$input->getName());

                                    $fieldSettings = $fieldsRepository->findBySecurekey($params[2])->getFieldTypeSettings();


                                    if($fp = $uploader->contentFileUpload($input->getData(), $fieldSettings['file_upload__file_upload_folder']))
                                    {

                                        //Get current field content
                                        $blockFieldObj = $blockField->getData();
                                        $blockFieldContents = $blockFieldObj->getFieldContent();

                                        $blockFieldContents[$params[0]] = $fp;
                                        unset($blockFieldContents[$input->getName()]);

                                        $blockFieldObj->setFieldContent($blockFieldContents);
                                    }

                                }
                            }


                        }
                    }


                    $em->persist($content);
                    $em->flush();
                    $storedContent = $cs->findSecureKeyById($content->getId());
                    $this->cacheContent($content->getId(),$cs,$em);
                    return $this->redirect(
                        $this->generateUrl('jfxninja_cms_frontend',array('uri' => $redirect))
                    );


        }


        return $this->render('JfxNinjaCMSBundle:Content:crud.html.twig', array(
            'form' => $form->createView(),
            'contentTitle' => $content->getName(),
            'mode' => $mode,
            'locale' => $locale,
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