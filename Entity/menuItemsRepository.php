<?php

namespace JfxNinja\CMSBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * menuItemsRepository
 *
 */
class menuItemsRepository extends EntityRepository
{

    private $locale = "en";
    private $defaultLocale = "en";

    public function setLocale($locale)
    {
        $this->locale = $locale;

        $defaultLocale = $this->getEntityManager()
            ->createQuery(
                'SELECT l.languageCode
                FROM JfxNinjaCMSBundle:Language l
                WHERE c.isDefault = 1'
            )
            ->getSingleResult();
        $this->defaultLocale = $defaultLocale['languageCode'];

    }

    public function findBySecurekey($securekey)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT c FROM JfxNinjaCMSBundle:MenuItem c WHERE c.securekey = :securekey'
            )->setParameter('securekey', $securekey)
            ->getSingleResult();
    }


    public function getItemsForListTable()
    {
        $data['type'] = "Field Types";
        $data['columns'] = array(
            "Name"=>"name",
            "Slug"=>"slug",
            "Mode"=>"mode",
            "Mapping"=>"mapAttached",
            "ID"=>"id"
        );
        $data['items'] = $this->getEntityManager()
            ->createQuery(
                'SELECT c.name, c.id, c.slug, c.mode, c.mapAttached, c.securekey
                FROM JfxNinjaCMSBundle:MenuItem c
                ORDER BY c.mapAttached, c.sort ASC'
            )
            ->getResult();

        foreach($data['items'] as &$d)
        {
            $d['name'] = $d['name'][$this->locale];
            $d['slug'] = $d['slug'][$this->locale];
        }

        return $data;
    }

    function getMenuSettings($mid)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT m.id, m.mode, c.id AS contentId, ct.id AS contentTypeId, p.id AS parentId
                FROM JfxNinjaCMSBundle:MenuItem m
                LEFT JOIN m.contentType ct
                LEFT JOIN m.content c
                LEFT JOIN m.parent p
                WHERE m.id = :id'
            )->setParameter('id', $mid)
            ->getSingleResult();

    }


}
