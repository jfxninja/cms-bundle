<?php

namespace SSone\CMSBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use SSone\CMSBundle\Entity\MenuItem;
use SSone\CMSBundle\Entity\Menu;
use SSone\CMSBundle\Entity\Content;


class AjaxValueListsController extends Controller
{

    public function getContentTypeCategoryFieldsAction(Request $request)
    {


        if ($request->isXmlHttpRequest() && $request->getMethod() == 'POST') {

            $menuTypeParams = explode("_",$request->get('menuType'));

            $ctId = (strpos($menuTypeParams[0],"list") !== false) ? $menuTypeParams[1] : 0;


            $categories = $this->getDoctrine()->getRepository('SSoneCMSBundle:Field')->getContentTypeCategoryFields($ctId);

            $response = array("code" => 100, "success" => true, "menuType"=>$ctId, "data"=>$categories);

        }

        return new JsonResponse($response);


    }


    public function getContentTypeCategoryRelatedFieldsAction(Request $request)
    {

        if ($request->isXmlHttpRequest() && $request->getMethod() == 'POST') {

            $category2FieldId = $request->get('category2FieldId');

            $fieldSettings = $this->getDoctrine()->getRepository('SSoneCMSBundle:Field')->fieldSettingsById($category2FieldId);

            $options = $this->getDoctrine()->getRepository('SSoneCMSBundle:Field')->getContentTypeCategoryFields($fieldSettings['fieldTypeSettings']['relatedcontent']['relatedcontent']);

            $response = array("code" => 100, "success" => true, "data"=>$options);
        }

        return new JsonResponse($response);


    }


    public function getModuleContentDisplayOptionsAction(Request $request)
    {

        if ($request->isXmlHttpRequest() && $request->getMethod() == 'POST') {

            $em = $this->getDoctrine();
            $contentService = $this->get('ssone.cms.content');

            $contentTypeId = $request->get('contentTypeId');

            $contentItemChoices = $contentService->findContentByContentTypeId($contentTypeId);

            $filterFieldOptions = $em->getRepository('SSoneCMSBundle:Field')->getContentTypeFields($contentTypeId,array("relatedcontent","text","date","checkbox","choice"));

            $orderbyFieldOptions = $em->getRepository('SSoneCMSBundle:Field')->getContentTypeFields($contentTypeId,array("relatedcontent","text","date"));

            $response = array("code" => 100, "success" => true,
                "contentItemChoices"=>$contentItemChoices,
                "filterFieldOptions"=>$filterFieldOptions,
                "orderbyFieldOptions"=>$orderbyFieldOptions

            );
        }

        return new JsonResponse($response);

    }


    public function getModuleFilterValueOptionsAction(Request $request)
    {

        if ($request->isXmlHttpRequest() && $request->getMethod() == 'POST') {

            $em = $this->getDoctrine();

            $contentService = $this->get('ssone.cms.content');

            $filterValueOptions = array();

            if($request->get('contentTypeId'))
            {
                $filterField = $em->getRepository('SSoneCMSBundle:Field')->find($request->get('contentTypeId'));

                if($filterField && $filterField->getFieldType()->getVariableName() == "relatedcontent")
                {
                    $settings = $filterField->getFieldTypeSettings();
                    $filterValueOptions = $contentService->findContentByContentTypeIdChoiceFormatted($settings['relatedcontent']['relatedcontent']);

                }
                else
                {
                    $filterValueOptions = "text";
                }

            }

            $response = array("code" => 100, "success" => true, "data"=>$filterValueOptions);
        }

        return new JsonResponse($response);

    }

}
