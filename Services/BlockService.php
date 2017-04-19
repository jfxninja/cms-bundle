<?php

namespace JfxNinja\CMSBundle\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use JfxNinja\CMSBundle\Entity\BlockField;
use JfxNinja\CMSBundle\Entity\Block;

/**
 * BlockService
 *
 */
class BlockService extends EntityRepository
{


    private $locale;
    private $defaultLocale ;
    private $localiser ;

    private $em;
    private $fu;
    private $ra;

    public function __construct(Localiser $localiser,EntityManager $em, FileUPloader $fu, RecordAuditor $ra)
    {
        $this->locale = $localiser->locale;
        $this->defaultLocale = $localiser->defaultLocale;
        $this->localiser = $localiser;

        $this->em = $em;
        $this->fu = $fu;
        $this->ra = $ra;
    }

    public function handleRemovedBlockFields($contentId,$blocks)
    {
        //query all block fields for secureids
        $originalBlockFields = $this->em
            ->createQuery(
                'SELECT bf.id, bf.securekey
                FROM JfxNinjaCMSBundle:BlockField bf
                LEFT JOIN bf.block b
                LEFT JOIN b.content c
                WHERE c.id = :id'
            )->setParameter('id', $contentId)
            ->getResult();


        foreach($originalBlockFields as $obf)
        {
            $found = false;

            foreach($blocks as $b)
            {
                foreach($b->getBlockFields() as $bf)
                {

                    if($bf->getSecurekey() == $obf['securekey'])
                    {
                        $found = true;
                    }
                }

            }
            if($found == false)
            {
                $bfToRemove = $this->em->getReference('JfxNinja\CMSBundle\Entity\BlockField', $obf['id']);
                $this->em->remove($bfToRemove);
            }


        }

    }


    /**
     * Check for changes to the content type - add remove content blocks as necessary
     *
     * @param $content
     */
    public function contentBlockManager($content)
    {
        //get fields for this content type
        $fields = $content->getContentType()->getVariableFields();

        //get blocks for this content type
        $blocks = $content->getBlocks();

        //Check if field has been removed and remove blocks as necessary
        foreach ($blocks as $b)
        {
            $foundBlock = false;
            foreach($fields as $f)
            {
                if($f->getVariableName() == $b->getField()->getVariableName()) $foundBlock = true;
            }
            if(!$foundBlock)
            {

                $content->removeBlock($b);
            }
        }

        //check if new field has been added and add block as necessary and set block sorts
        foreach ($fields as $f)
        {
            $foundBlock = false;
            foreach($blocks as $b)
            {
                //if($b->getLocale() != $locale) continue;
                if($b->getField()->getVariableName() == $f->getVariableName())
                {
                    $foundBlock = true;
                    $b->setSort($f->getSort());
                }

            }
            if(!$foundBlock)
            {

                $newBlock = new Block();
                $newBlock->setField($f);
                $newBlock->setSort($f->getSort());

                $newBlockField = new BlockField();

                $newBlockField->setBlock($newBlock);
                $newBlock->addBlockField($newBlockField);

                $newBlock->setContent($content);
                $content->addBlock($newBlock);

            }


        }

    }


    /**
     * Check for changes to the content type - add remove content blocks as necessary
     * @param $contentType
     */
    public function contentTypeBlockManager($contentType)
    {
        //get fields for this content type
        $fields = $contentType->getAttributeFields();

        //get blocks for this content type
        $blocks = $contentType->getBlocks();

        //Check if field has been removed and remove blocks as necessary
        foreach ($blocks as $b)
        {
            $foundBlock = false;
            foreach($fields as $f)
            {
                if($f->getVariableName() == $b->getField()->getVariableName()) $foundBlock = true;
            }
            if(!$foundBlock)
            {

                $contentType->removeBlock($b);
            }
        }

        //check if new field has been added and add block as necessary and set block sorts
        foreach ($fields as $f)
        {
            $foundBlock = false;
            foreach($blocks as $b)
            {
                //if($b->getLocale() != $locale) continue;
                if($b->getField()->getVariableName() == $f->getVariableName())
                {
                    $foundBlock = true;
                    $b->setSort($f->getSort());
                }

            }
            if(!$foundBlock)
            {

                $newBlock = new Block();
                $newBlock->setField($f);
                $newBlock->setSort($f->getSort());

                $newBlockField = new BlockField();

                $newBlockField->setBlock($newBlock);
                $newBlock->addBlockField($newBlockField);

                $newBlock->setContentType($contentType);
                $contentType->addBlock($newBlock);

            }


        }

    }


    public function handleUploadBlocks($form)
    {

        $fieldsRepository = $this->em->getRepository('JfxNinjaCMSBundle:Field');

        foreach ($form['blocks']  as $block) {

            foreach($block['blockFields'] as $blockField)
            {

                foreach($blockField['fieldContent'] as $input)
                {

                    if(strpos($input->getName(),'_fileupload') !== false)
                    {

                        $params = explode("_",$input->getName());

                        $fieldSettings = $fieldsRepository->findBySecurekey($params[2])->getFieldTypeSettings();

                        $file = array();

                        if($fp = $this->fu->contentFileUpload($input->getData(), $fieldSettings['file_upload__file_upload_folder']."/".$this->makeURLSafe($form['name']->getData())))
                        {

                            $file['filePath'] = $fp;

                        }
                        elseif($blockField['fieldContent'][$params[0]])
                        {
                            $file['filePath'] = $blockField['fieldContent'][$params[0]]->getData();

                        }

                        if($image = $this->is_image(__DIR__.'/../../../../web/assets/'.$file['filePath']))
                        {

                            $file['width']  = $image[0];
                            $file['height'] = $image[1];
                            $file['type']   = $image[2];
                            $file['attr']   = $image[3];

                        }



                        //Get current field content
                        $blockFieldObj = $blockField->getData();
                        $blockFieldContents = $blockFieldObj->getFieldContent();

                        $blockFieldContents[$params[0]] = $file;
                        unset($blockFieldContents[$input->getName()]);
                        $blockFieldObj->setFieldContent($blockFieldContents);
                        //update the record

                    }
                }

            }

        }

    }


    public function is_image($path)
    {
        $image = @getimagesize($path);
        $image_type = $image[2];

        if(in_array($image_type , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP)))
        {
            return $image;
        }
        return false;
    }

    private function makeURLSafe($input)
    {
        return preg_replace("/[^a-zA-Z0-9\-\_\.]+/", "", $input);
    }

}
