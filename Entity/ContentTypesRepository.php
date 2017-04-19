<?php

namespace JfxNinja\CMSBundle\Entity;

use Doctrine\ORM\EntityRepository;


class ContentTypesRepository extends EntityRepository
{

    public function findBySecurekey($securekey)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT c FROM JfxNinjaCMSBundle:ContentType c WHERE c.securekey = :securekey'
            )->setParameter('securekey', $securekey)
            ->getSingleResult();
    }

    public function getContentListHeading($securekey)
    {
        if($securekey == "not-specified")
        {
            $firstCt = $this->getEntityManager()
                ->createQuery(
                    'SELECT ct.securekey
                    FROM JfxNinjaCMSBundle:ContentType ct
                    ORDER BY ct.name ASC'
                )
                ->setMaxResults(1)
                ->getResult();

            $contentType = current($firstCt);
            $securekey = $contentType['securekey'];

        }

        try {
            $firstContentType = $this->getEntityManager()
                ->createQuery(
                    'SELECT c FROM JfxNinjaCMSBundle:ContentType c WHERE c.securekey = :securekey'
                )->setParameter('securekey', $securekey)
                ->getSingleResult();
        } catch (\Exception $e)
        {
            $firstContentType = null;
        }

        return $firstContentType;

    }

    public function findAllAsObjs()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT c FROM JfxNinjaCMSBundle:ContentType c WHERE c.id = 24'
            )
            ->getResult("Query::HYDRATE_SINGLE_SCALAR");
    }


    public function getItemsForListTable()
    {
        $data['type'] = "Content Types";
        $data['columns'] = array(
            "Name"=>"name",
            "Content Template Path" => "contentTemplatePath",
            "Last Modified"=>"modifiedAt",
            "Last Modified By"=>"modifiedBy",
            );
        $data['columnTitles'] = array("Name", "Template Path", "ID");
        $data['items'] = $this->getEntityManager()
            ->createQuery(
                'SELECT c.name, c.contentTemplatePath, c.id, c.securekey, c.modifiedBy,  c.modifiedAt FROM JfxNinjaCMSBundle:ContentType c ORDER BY c.name ASC'
            )
            ->getResult();
        return $data;
    }



    public function buildContentTypeMenu()
    {

        $contentType = array();
        $menus = array();

        $content = $this->getEntityManager()
            ->createQuery(
            'SELECT ct.id, ct.name, ct.securekey, c.id AS contentId
            FROM JfxNinjaCMSBundle:ContentType ct
            LEFT JOIN ct.content c
            WHERE ct.hideFromMenus != 1
            ORDER BY ct.name ASC'
        )
            ->getResult();

        foreach($content as $c)
        {

            if($c['contentId'])
            {
                $contentType[$c['name']]['items'][] = $c;
                $contentType[$c['name']]['securekey'] = $c['securekey'];
                $contentType[$c['name']]['id'] = $c['id'];
            }
            else
            {
                $contentType[$c['name']]['items'] = array();
                $contentType[$c['name']]['securekey'] = $c['securekey'];
                $contentType[$c['name']]['id'] = $c['id'];
            }
        }

        foreach($contentType as $k=>$ct)
        {

            $menus[$k] = array("name"=>"" . $k . "s (count: ".count($ct['items']) . ")", "link"=>"/admin/content/".$ct['securekey']);

            $attributeFields = $this->getEntityManager()
                ->createQuery(
                    'SELECT f.id
                    FROM JfxNinjaCMSBundle:Field f
                    LEFT JOIN f.contentTypeByAttribute ct
                    WHERE ct.id = :id'
                )
                ->setParameter("id", $ct['id'])
                ->getResult();

            if(count($attributeFields) > 0)
            {
                $menus[$k."list"] = array("name"=>"Edit " . $k . " list page", "link"=>"/admin/content/attributes/".$ct['securekey']);
            }

        }

        return $menus;

    }


    public function getContentTypeChoiceOptions()
    {
        $query = 'SELECT i.name, i.id FROM JfxNinjaCMSBundle:ContentType i ORDER BY i.name ASC';
        $items = $this->getEntityManager()
            ->createQuery($query)
            ->getResult();

        $entityItems = array();
        foreach($items as $i)
        {
            $entityItems[$i['id']] = $i['name'];
        }

        return $entityItems;

    }


}
