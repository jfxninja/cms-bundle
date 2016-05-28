<?php

namespace SSone\CMSBundle\Services;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentService extends EntityRepository
{

    private $locale;
    private $defaultLocale ;
    private $localiser ;
    private $em;
    private $bs;
    private $rs;

    public function __construct(Localiser $localiser,EntityManager $em, BlockService $bs, RecordAuditor $rs)
    {
        $this->locale = $localiser->locale;
        $this->defaultLocale = $localiser->defaultLocale;
        $this->localiser = $localiser;
        $this->em = $em;
        $this->bs = $bs;
        $this->rs = $rs;
    }


    /**
     * @param $activeMenuItem
     * @param $uri
     * @return array|mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function retrieveMenuContent($activeMenuItem,$uri)
    {

        $content = array();
        $urlParts = explode("/",$uri);
        $contentSlug = "";

        //is this a list
        //either category menu url url remainder = list
        if($activeMenuItem['mode'] == "single")
        {

            $content = $this->getSingleItem($activeMenuItem['contentId']);
            if(!$content)
            {
                //content for the menu doesnt exist
                throw new NotFoundHttpException("Page not found");
            }

        }
        elseif($activeMenuItem['mode'] == "list")
        {

            //This can be speed improved to filter via block lookup first.
            $content = $this->getListItems($activeMenuItem['contentTypeId'],$activeMenuItem['slug']);

            //If this is a category menu
            if($activeMenuItem['ctc1FieldId'])
            {

                $content = $this->filterContentByMenuCategories($content,$activeMenuItem);

            }
            if(!$content)
            {
                //empty lists should be handled in the templates
                //print("Empty list"); //TODO:JW Handle empty list
            }


            if(!isset($content['attributes']['slug']))
            {
                $content['attributes']['slug'] = "";
            }

        }
        elseif($activeMenuItem['mode'] == "listitem")
        {

            $content = $this->getListItem($activeMenuItem['contentTypeId'],end($urlParts));

            if(!$content)
            {
                //url remainder does not match a list item in this category
                throw new NotFoundHttpException("Page not found");
            }


        }
        elseif($activeMenuItem['mode'] == "category")
        {

        }
        return $content;
    }


    /**
     * @param $securekey
     * @return mixed
     */
    public function findBySecurekey($securekey)
    {
        return $this->em
            ->createQuery(
                'SELECT c FROM SSoneCMSBundle:Content c WHERE c.securekey = :securekey'
            )->setParameter('securekey', $securekey)
            ->getSingleResult();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findSecureKeyById($id)
    {
        return $this->em
            ->createQuery(
                'SELECT c.securekey FROM SSoneCMSBundle:Content c WHERE c.id = :id'
            )->setParameter('id', $id)
            ->getSingleResult();
    }


    /**
     * @param $ctsecurekey
     * @return mixed
     */
    public function getItemsForListTable($ctsecurekey)
    {

        if($ctsecurekey == "not-specified")
        {
            $firstCt = $this->em
                ->createQuery(
                    'SELECT ct.securekey
                    FROM SSoneCMSBundle:ContentType ct
                    ORDER BY ct.name ASC'
                )
                ->setMaxResults(1)
                ->getResult();

            $contentType = current($firstCt);
            $ctsecurekey = $contentType['securekey'];

        }


        $data['type'] = "Content";
        $data['columns'] = array(
            "Name"=>"name",
            "Slug"=>"slug",
            "Last Modified"=>"modifiedAt",
            "Last Modified By"=>"modifiedBy",
            );
        $data['columnTitles'] = array("Name", "ID");
        $data['items'] = $this->em
            ->createQuery(
                'SELECT c.name, c.slug, c.modifiedBy,  c.modifiedAt, c.securekey, ct.securekey AS ctSecurekey
                FROM SSoneCMSBundle:Content c
                Left JOIN c.contentType ct
                WHERE ct.securekey = :securekey
                ORDER BY c.name ASC'
            )->setParameter('securekey', $ctsecurekey)
            ->getResult();

        foreach($data['items'] as &$item)
        {
            $item = $this->localiser->setMultiLanguageFields($item, array("slug"),$this->defaultLocale);
        }

        //todo:jw iterate with multilanguage fields service to translate
        return $data;
    }


    /**
     * @param $id
     * @return array
     */
    public function getSingleItem($id)
    {
        $c = $this->em
            ->createQuery(
                'SELECT
                  c.name AS contentName,
                  c.id AS contenId,
                  c.slug,
                  c.content,
                  c.createdBy,
                  ct.contentTemplatePath AS template
                  FROM SSoneCMSBundle:Content c
                  LEFT JOIN c.contentType ct
                  WHERE c.id = :id'
            )
            ->setParameter('id', $id)
            ->getSingleResult();


        if($c['content'][$this->locale])
        {
            $blocks =  $c['content'][$this->locale];
        }
        else
        {
            $blocks =  $c['content'][$this->defaultLocale];
        }

        unset($c['content']);

        $blocks['attributes'] = $c;

        return $blocks;

    }


    /**
     * @param $contentTypeId
     * @param $menuSlug
     * @return array
     */
    public function getListItems($contentTypeId,$menuSlug)
    {

        //Get content type
        $contentAttributes = $this->em
            ->createQuery(
                'SELECT
                  c.id,
                  c.name,
                  c.listTemplatePath AS template
                  FROM SSoneCMSBundle:ContentType c
                  WHERE c.id = :id'
            )
            ->setParameter('id', $contentTypeId)
            ->getSingleResult();

        $contentAttributes = array_merge(
                                    $contentAttributes,
                                    $this->getBlocks("contentType",$contentAttributes['id'],$this->locale));

        //Get Items
        $contentItems = $this->em
            ->createQuery(
                'SELECT
                  c.id,
                  c.name,
                  c.slug,
                  c.content,
                  ct.id AS contentTypeId
                  FROM SSoneCMSBundle:content c
                  LEFT JOIN c.contentType ct
                  WHERE ct.id = :id
                  ORDER BY c.name ASC' //TODO:JW implement configurable sort option
            )
            ->setParameter('id', $contentTypeId)
            ->getResult();


        $listItems = array();

        foreach ($contentItems as $ci) {

            if($ci['content'][$this->locale])
            {
                $listItems[$ci['id']] =  $ci['content'][$this->locale];
            }
            else
            {
                $listItems[$ci['id']] =  $ci['content'][$this->defaultLocale];
            }

            $ci = $this->localiser->setMultiLanguageFields($ci,array("slug"),$this->locale);
            $listItems[$ci['id']]['slug'] = $ci['slug'];
            $listItems[$ci['id']]['link'] = $menuSlug . "/" .$ci['slug'];

        }

        return array("attributes"=>$contentAttributes, "listItems"=>$listItems);

    }


    public function getListItem($contentTypeId,$slug)
    {

        //Get Items
        $content = $this->em
            ->createQuery(
                'SELECT
                  c.id,
                  c.name,
                  c.slug,
                  c.content,
                  ct.id AS contentTypeId,
                  ct.contentTemplatePath AS template
                  FROM SSoneCMSBundle:content c
                  LEFT JOIN c.contentType ct
                  WHERE ct.id = :id AND c.slug LIKE :slug'
            )
            ->setParameter('id', $contentTypeId)
            ->setParameter('slug', "%".$slug."%")
            ->getResult();

        foreach($content as $c)
        {
            $localisedItem = $this->localiser->setMultiLanguageFields($c,array("slug"),$this->locale);
            if($slug == $localisedItem['slug'])
            {
                //$listItems[$ci['id']] = $this->getBlocks("content",$ci['id'],$this->locale);
                if($c['content'][$this->locale])
                {
                    $blocks =  $c['content'][$this->locale];
                }
                else
                {
                    $blocks =  $c['content'][$this->defaultLocale];
                }
                unset($c['content']);
                return array("attributes"=>$c, "blocks"=>$blocks);

            }

        }



    }


    /**
     * @param $contentTypeId
     * @param $filterFieldId
     * @param $filterValue
     * @param $SortByFieldId
     * @param $contentOrderByValue
     * @return array
     */
    function getModuleListContent($contentTypeId,$filterFieldId,$filterValue,$SortByFieldId,$contentOrderByValue)
    {

        $listItems = array();

        if($filterFieldId)
        {
            $blocks = $this->em
                ->createQuery(
                    "SELECT
                       b.id,
                       f.id AS fieldId,
                       f.isRepeatable,
                       f.variableName,
                       bf.fieldContent,
                       c.id AS contentId
                     FROM SSoneCMSBundle:Block b
                     LEFT JOIN b.blockFields bf
                     LEFT JOIN b.content c
                     LEFT JOIN b.field f
                     WHERE f.id = :fid"
                )->setParameter('fid',$filterFieldId)
                ->getResult();

            $blocks = $this->filterBlocksByFieldValue($blocks,$filterValue);

            foreach($blocks as $block)
            {
                $listItems[] = $this->getSingleItem($block['contentId']);

            }
        }
        else
        {
            //Get Items
            $contentItems = $this->em
                ->createQuery(
                    'SELECT
                      c.id,
                      c.name,
                      c.slug,
                      c.content,
                      ct.id AS contentTypeId
                      FROM SSoneCMSBundle:content c
                      LEFT JOIN c.contentType ct
                      WHERE ct.id = :id
                      ORDER BY c.name ASC' //TODO:JW implement configurable sort option
                )
                ->setParameter('id', $contentTypeId)
                ->getResult();

            foreach ($contentItems as $ci) {

                if($ci['content'][$this->locale])
                {
                    $listItems[$ci['id']] =  $ci['content'][$this->locale];
                }
                else
                {
                    $listItems[$ci['id']] =  $ci['content'][$this->defaultLocale];
                }

                $ci = $this->localiser->setMultiLanguageFields($ci,array("slug"),$this->locale);
                $listItems[$ci['id']]['attributes']['slug'] = $ci['slug'];

            }

        }



        if($SortByFieldId)
        {

            $field = $this->em->getRepository('SSoneCMSBundle:Field')->find($SortByFieldId);

            $listItems = $this->arraySort($listItems,$field->getVariableName(),$contentOrderByValue);
        }

        return $listItems;

    }


    private function arraySort ($array, $key, $direction)
    {

        $sorter=array();
        $ret=array();

        reset($array);

        foreach ($array as $ii => $va) {
            $sorter[$ii]=$va[$key];
        }

        if($direction == "asc")
        {
            asort($sorter);
        }
        else
        {
            arsort($sorter);
        }


        foreach ($sorter as $ii => $va) {
            $ret[$ii]=$array[$ii];
        }

        $array=$ret;

        return $array;

    }


    /**
     * @param $blocks
     * @param $filterValue
     * @return mixed
     */
    public function filterBlocksByFieldValue($blocks,$filterValue)
    {


        foreach($blocks as $k=>$block)
        {

            //handle repeatable fields
            if(is_array($block['fieldContent']))
            {
                $found = false;
                foreach($block['fieldContent'] as $variableName=>$value)
                {
                    if($value == $filterValue)
                    {
                        $found = true;
                    }
                }

                if(!$found) unset($blocks[$k]);

            }
            else
            {
                if(!$blocks['fieldContent'] == $filterValue)
                {
                    unset($blocks[$k]);
                }

            }


        }

        return $blocks;

    }


    /**
     * The following 3 function handle filtering content items for category menus
     *
     * @param $content
     * @param $activeMenu
     * @return mixed
     */
    public function filterContentByMenuCategories($content,$activeMenu)
    {

        if(!isset($activeMenu['urlParts'])) return $content;

        if($activeMenu['ctc1FieldId'])
        {
            //first get the variable name
            $field = $this->em
                ->createQuery(
                    'SELECT
                      f.id,
                      f.variableName,
                      ft.variableName as fieldType
                      FROM SSoneCMSBundle:Field f
                      LEFT JOIN f.fieldType ft
                      WHERE f.id = :id'
                )
                ->setParameter('id', $activeMenu['ctc1FieldId'])
                ->getSingleResult();

            $content = $this->filterContentByURLFilter($content,$field,$activeMenu['urlParts'][0]);
        }

        //This relies on 2 only being set if 1 is $activeMenu['urlParts'][1]
        if($activeMenu['ctc2FieldId'])
        {
            //first get the variable name
            $field = $this->em
                ->createQuery(
                    'SELECT
                      f.id,
                      f.variableName,
                      ft.variableName as fieldType
                      FROM SSoneCMSBundle:Field f
                      LEFT JOIN f.fieldType ft
                      WHERE f.id = :id'
                )
                ->setParameter('id', $activeMenu['ctc2FieldId'])
                ->getSingleResult();

            $content = $this->filterContentByURLFilter($content,$field,$activeMenu['urlParts'][1]);
        }


        return $content;


    }

    /**
     * @param $content
     * @param $field
     * @param $filterValue
     * @return mixed
     */
    public function filterContentByURLFilter($content,$field,$filterValue)
    {


        foreach($content['listItems'] as $k=>$c)
        {

            if(!isset($c[$field['variableName']]))
            {
                unset($content['listItems'][$k]);
                continue;
            }

            //handle repeatable fields
            if(is_array($c[$field['variableName']]))
            {
                $found = false;
                foreach($c[$field['variableName']] as $v)
                {

                    if($this->compareFieldToURLFilterValue($field,$v,$filterValue))
                    {
                        $found = true;
                    }
                }

                if(!$found) unset($content['listItems'][$k]);

            }
            else
            {


                if(!$this->compareFieldToURLFilterValue($field,$c,$filterValue))
                {
                    unset($content['listItems'][$k]);
                }

            }


        }

        return $content;

    }



    /**
     * @param $field
     * @param $v
     * @param $filterValue
     * @return bool
     */
    private function compareFieldToURLFilterValue($field,$v,$filterValue)
    {
        $match = false;

        if($field['fieldType'] == "relatedcontent")
        {
            //print($this->urlSafe($v['contentSlug']) ." = ". $filterValue);
            if($this->urlSafe($v['contentSlug']) == $filterValue)
            {
                $match = true;
            }
        }
        elseif($field['fieldType'] == "choice")
        {

            if($this->urlSafe($v[$field['variableName']]) == $filterValue)
            {
                $match = true;
            }
        }

        return $match;
    }

    public function urlSafe($unsafe)
    {
        $strip = array(" ", "~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
            "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
            "â€”", "â€“", ",", "<", ".", ">", "/", "?");
        $clean = str_replace($strip, "-", $unsafe);

        return $clean;

    }


    /**
     * @param $entityName
     * @param $id
     * @param $locale
     * @return array
     */
    public function getBlocks($entityName,$id,$locale="")
    {

        if(!$locale) $locale = $this->locale;

        $content = array();

        //First select the blocks
        $blocks = $this->em
            ->createQuery(
                "SELECT
                  b.id,
                  f.variableName,
                  e.id AS contentId,
                  e.name AS contentName,
                  e.slug AS contentSlug
                FROM SSoneCMSBundle:{$entityName} e
                LEFT JOIN e.blocks b
                LEFT JOIN b.field f
                WHERE e.id = :id
                ORDER BY b.sort"
            )
            ->setParameter('id', $id)
            ->getResult();



        //Select blockfields
        foreach ($blocks as $b) {

            $content['contentSlug'] = $this->localiser->translateMultiLanguageField($b['contentSlug'],$locale);

            $blockFields = $this->em
                ->createQuery(
                    "SELECT
                  b.id,
                  f.isRepeatable,
                  f.variableName,
                  f.fieldTypeSettings,
                  ft.variableName as fieldType,
                  bf.fieldContent
                FROM SSoneCMSBundle:Block b
                LEFT JOIN b.blockFields bf
                LEFT JOIN b.field f
                LEFT JOIN f.fieldType ft
                WHERE b.id = :id
                ORDER BY bf.sort"
                )
                ->setParameter('id', $b['id'])
                ->getResult();



            //Build content variable
            foreach($blockFields as $bfk=>$bf)
            {


                if(isset($bf['fieldContent']))
                {

                    if($bf['fieldType'] == "relatedcontent")
                    {

                        $content[$bf['variableName']][$bfk] = $this->getBlocks($entityName,$bf['fieldContent'][$bf['variableName']],$locale);
                        continue;
                    }



                    foreach($bf['fieldContent'] as $k=>$input)
                    {
                        $fieldIsTranslatable = false;
                        if(($bf['fieldType'] == "text" && $bf['fieldTypeSettings']['text']['texttrans'])) $fieldIsTranslatable = true;
                        if(($bf['fieldType'] == "textarea" && $bf['fieldTypeSettings']['textarea']['textareatrans'])) $fieldIsTranslatable = true;
                        if(($bf['fieldType'] == "wysiwyg" && $bf['fieldTypeSettings']['wysiwyg']['wysiwygtrans'])) $fieldIsTranslatable = true;
                        if(($bf['fieldType'] == "form" && $bf['fieldTypeSettings']['form']['multilanguageform'])) $fieldIsTranslatable = true;


                        //If this is a multi-language field
                        if(is_array($input) && $fieldIsTranslatable)
                        {
                            //try the current language
                            if(isset($input[$locale]))
                            {
                                $content[$bf['variableName']][$bfk][$k] = $input[$locale];
                            }
                            //Otherwise fall back to the default language
                            else
                            {
                                $content[$bf['variableName']][$bfk][$k] = $input[$this->defaultLocale];
                            }
                        }
                        //Otherwise this is a non localised field
                        else
                        {

                            $content[$bf['variableName']][$bfk][$k] = $input;
                            //print($bf['variableName'] . " - " .$bfk. " - " .$k."\r\n");
                        }
                    }



                    //If field is not repeatable set the value as a string not array
                    if(!$bf['isRepeatable'] && $bf['fieldType'] != "embeded")
                    {
                        $value = $content[$bf['variableName']][$bfk][$k];

                        $content[$bf['variableName']] = $value;
                    }

                    if($bf['fieldType'] == "form" )
                    {
                        $content['FORMS'][] = $bf['variableName'];
                    }

                }

            }

        }


        return $content;

    }


    /**
     * @return array
     */
    public function getAllForGroupedChoiceOptions()
    {

        $contentLibrary = array();

        $contentTypes = $this->em
            ->createQuery(
                'SELECT ct.name, ct.id, ct.hideFromMenus
                FROM SSoneCMSBundle:ContentType ct
                WHERE ct.hideFromMenus != 1
                ORDER BY ct.name ASC'
            )
            ->getResult();

        foreach($contentTypes as $ct)
        {

            $items = $this->em
                ->createQuery(
                    'SELECT c.name, c.id, ct.id as ctId
                    FROM SSoneCMSBundle:Content c
                    LEFT JOIN c.contentType ct
                    WHERE ct.id = :ctId
                    ORDER BY c.name ASC'
                )
                ->setParameter('ctId', $ct['id'])
                ->getResult();

          $contentLibrary[$ct['id']]['name'] = $ct['name'];
          $contentLibrary[$ct['id']]['items'] = $items;
        }


        return $contentLibrary;

    }


    /**
     * @param $contentTypeId
     * @return array
     */
    public function getContentTypeFieldsAsArray($contentTypeId)
    {

        $data = $this->em
            ->createQuery(
                'SELECT f.name, f.variableName f.id, f.securekey, f.content
                FROM SSoneCMSBundle:Fields f
                WHERE f.fk_contentType_id = :contentTypeID
                ORDER BY c.name ASC'
            )
            ->setParameter('contentTypeId', $contentTypeId)
            ->getResult();

        return $data;

    }


    public function getFormsAsChoiceOptions()
    {

        $forms =  $this->em
            ->createQuery(
                'SELECT
                f.id, f.name
                FROM SSoneCMSBundle:CMSForm f'
            )
            ->getResult();

        $options = array();

        foreach($forms as $f)
        {
            $options[$f['id']] = $f['name'];
        }

        return $options;

    }

    /**
     * @param $id
     * @return array
     */
    public function findContentByContentTypeId($id)
    {

        $items = $this->em
            ->createQuery(
                'SELECT c.name, c.id, ct.id as ctId
                FROM SSoneCMSBundle:Content c
                LEFT JOIN c.contentType ct
                WHERE ct.id = :ctId
                ORDER BY c.name ASC'
            )
            ->setParameter('ctId', $id)
            ->getResult();



        return $items;

    }


    /**
     * @param $id
     * @return array
     */
    public function findContentByContentTypeIdChoiceFormatted($id)
    {

        $items = array();

        $results = $this->em
            ->createQuery(
                'SELECT c.name, c.id, ct.id as ctId
                FROM SSoneCMSBundle:Content c
                LEFT JOIN c.contentType ct
                WHERE ct.id = :ctId
                ORDER BY c.name ASC'
            )
            ->setParameter('ctId', $id)
            ->getResult();

        foreach($results as $k=>$value)
        {
            $items[$value['id']] = $value['name'];
        }


        return $items;

    }


    public function convertChoiceOptionsStringToArray($stringOptions)
    {
        $options = array();

        $stringOptions = rtrim($stringOptions, ";");


        foreach(explode(";",$stringOptions) as $stringOption)
        {

            $arrayOption = explode("=",$stringOption);

            $options[trim($arrayOption[1])] = trim($arrayOption[0]);

        }
        return $options;
    }


    public function saveContent($content,$form)
    {

        $this->rs->auditRecord($content);


        //Audit blocks and fields
        foreach($content->getBlocks() as $block)
        {
            $this->rs->auditRecord($block);

            foreach($block->getBlockFields() as $blockField)
            {

                $this->rs->auditRecord($blockField);

            }

        }

        $this->bs->handleUploadBlocks($form);


        $this->em->persist($content);
        $this->em->flush();

        return $this->cacheContent($content->getId());

    }


    private function cacheContent($id)
    {

        $languages = $this->em->getRepository('SSoneCMSBundle:Language')->findAll();

        foreach($languages as $l)
        {
            $lc = $l->getLanguageCode();
            $blocks[$lc] = $this->getBlocks("content",$id,$lc);
        }

        $content = $this->em->getRepository('SSoneCMSBundle:Content')->find($id);

        $content->setContent($blocks);

        $this->em->flush();

        return $blocks;

    }

}
