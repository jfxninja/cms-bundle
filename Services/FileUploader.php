<?php

namespace JfxNinja\CMSBundle\Services;

/**
 * A simple service to standarddize file uploading
 */
class FileUploader
{

    public function templateUpload($file,$folder)
    {
        if($file)
        {
            $extension = ".html.twig";

            $tempfn  = explode( '.', $file->getClientOriginalName() );
            if(count($tempfn) > 1)
            {
                array_pop( $tempfn );
                $tempfn = implode( '.', $tempfn );
            }


            $fileName = $this->makeSafe($tempfn).$extension;

            $path  = __DIR__.'/../Resources/theme/views/'.$folder;

            $this->upload($file,$path,$fileName);

            return $folder.'/'.$fileName;
        }

    }

    public function contentFileUpload($file,$folder)
    {
        $folder = trim($folder,"/");

        if($file)
        {

            $fileName = $this->makeSafe($file->getClientOriginalName());

            $path  = __DIR__.'/../../../../web/assets/'.$folder;

            $this->upload($file,$path,$fileName);

            $fp = '/'.$folder.'/'.$fileName;

            return $fp;
        }

    }

    private function upload($file,$path,$fileName)
    {

            $file->move($path, $fileName);
    }

    private function makeSafe($input)
    {
        return preg_replace("/[^a-zA-Z0-9\-\_\.]+/", "", $input);
    }

}