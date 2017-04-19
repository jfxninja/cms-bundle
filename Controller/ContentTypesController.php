<?php

namespace JfxNinja\CMSBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JfxNinja\CMSBundle\Entity\ContentType;
use JfxNinja\CMSBundle\Form\Type\ContentTypeTYPE;
use Doctrine\Common\Collections\ArrayCollection;


class ContentTypesController extends Controller
{

    public function indexAction()
    {

        $contentType = $this->getDoctrine()
            ->getRepository('JfxNinjaCMSBundle:ContentType')
            ->getItemsForListTable();


        return $this->render(
            'JfxNinjaCMSBundle:AdminTemplates:standardList.html.twig',
            array(
                "items" => $contentType,
                "title" => "Content Types"
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

        $locale                 = $request->getLocale();
        $em                     = $this->getDoctrine()->getManager();
        $cmsInputTypeService    = $this->get('jfxninja.cms.input_type');
        $inputTypeSetupOptions  = $cmsInputTypeService->getInputTypeSetupOptions();
        $inputTypes             = $cmsInputTypeService->getInputTypes();

        if($mode == "new")
        {
            $contentType = new contentType();
        }
        else
        {
            $contentType = $this->getDoctrine()->getRepository('JfxNinjaCMSBundle:ContentType')->findBySecurekey($securekey);


            // Create an ArrayCollection of the current field variable objects in the database
            // to compare which if any have ben removed.
            $originalVariableFields = new ArrayCollection();

            foreach ($contentType->getVariableFields() as $field) {
                $originalVariableFields->add($field);
            }
            // Create an ArrayCollection of the current field attribute objects in the database
            // to compare which if any have ben removed.
            $originalAtrributeFields = new ArrayCollection();

            foreach ($contentType->getAttributeFields() as $field) {
                $originalAtrributeFields->add($field);
            }

        }


        $form = $this->createForm(new ContentTypeTYPE($mode, $inputTypes, $inputTypeSetupOptions, $locale), $contentType);


        $form->handleRequest($request);
        if ($form->isValid())
        {


            $auditor = $this->get('jfxninja.cms.recordauditor');
            $auditor->auditRecord($contentType);


            if($contentType->getVariableFields())
            {
                foreach($contentType->getVariableFields() as $vField)
                {
                    $this->setBlockSort($vField);
                    $auditor->auditRecord($vField);

                }
            }


            if($contentType->getAttributeFields())
            {
                foreach($contentType->getAttributeFields() as $aField)
                {
                    $this->setBlockSort($aField);
                    $auditor->auditRecord($aField);

                }
            }



            switch($mode)
            {

                case "new":
                    $em->persist($contentType);
                    break;

                case "delete":
                    $em->remove($contentType);
                    break;

                case "edit":
                    foreach ($originalVariableFields as $f)
                    {
                        if(false === $contentType->getVariableFields()->contains($f))
                        {
                            $em->remove($f);
                        }
                    }

                    foreach ($originalAtrributeFields as $f)
                    {
                        if(false === $contentType->getAttributeFields()->contains($f))
                        {
                            $em->remove($f);
                        }
                    }
                    $this->handleUploads(
                                    $form,
                                    array(
                                        "file_contentTemplatePath"=>"setContentTemplatePath",
                                        "file_listTemplatePath"=>"setListTemplatePath",
                                        "file_categoryPageTemplatePath"=>"setCategoryPageTemplatePath"
                                    ),
                                    $contentType);
                    break;

            }


            $em->flush();

            return $this->redirect(
                $this->generateUrl('jfxninja_cms_admin_contentTypes_list')
            );
        }


        return $this->render('JfxNinjaCMSBundle:ContentTypes:crud.html.twig', array(
            'form' => $form->createView(),
            'mode' => $mode,
            'inputTypeSetupOptions' => $inputTypeSetupOptions,
            'contentType' => $contentType->getName(),
            'locale' => $locale,

        ));



    }


    /**
     * Get all Field setup option for form builder
     * @return mixed
     */
    private function getAllFieldSetupOptions()
    {

        return $this->getDoctrine()
            ->getRepository('JfxNinjaCMSBundle:FieldSetupOptions')
            ->getAllFieldSetupOptions();

    }

    private function setBlockSort($field)
    {
        foreach($field->getBlocks() as $block)
        {
            $block->setSort($field->getSort());
        }
    }



    /**
     * @param $form
     * @param $fields
     * @param $contentType
     */
    private function handleUploads($form,$fields,$contentType)
    {

        $uploader = $this->get('jfxninja.cms.fileuploader');

        foreach($fields as $field=>$function)
        {
            $folder = $contentType->getSlug($this->container->getParameter('locale'));

            if($fp = $uploader->templateUpload($form[$field]->getData(), $folder))
            {
                $contentType->$function($fp);
            }

        }

    }




}