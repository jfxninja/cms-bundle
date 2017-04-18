<?php

namespace JfxNinja\CMSBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class WYSIWYGImageUploadController extends Controller
{
    public function storeImageAction(Request $request, $sfid)
    {
        $em = $this->getDoctrine()->getManager();
        $fieldsRepository = $em->getRepository('JfxNinjaCMSBundle:Field');

        $uploader = $this->get('jfxninja.cms.fileuploader');

        //get the upload folder

        $fieldSettings = $fieldsRepository->findBySecurekey($sfid)->getFieldTypeSettings();
        $folder = $fieldSettings['wysiwyg']['wysiwygUploadPath'];

        $savedFile = array();
        foreach($request->files as $file) {
            if($fp = $uploader->contentFileUpload($file, $folder));
            {
                $savedFile = array('filelink' => '/assets'.$fp);
            }
        }

        return new Response(
            json_encode($savedFile),
            Response::HTTP_OK,
            array('content-type' => 'application/json')
        );

    }

}