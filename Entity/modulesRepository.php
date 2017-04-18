<?php

namespace JfxNinja\CMSBundle\Entity;

use Doctrine\ORM\EntityRepository;


class modulesRepository extends EntityRepository
{


    public function findBySecurekey($securekey)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT c FROM JfxNinjaCMSBundle:Module c WHERE c.securekey = :securekey'
            )->setParameter('securekey', $securekey)
            ->getSingleResult();
    }


    public function getItemsForListTable()
    {
        $data['type'] = "Field Types";
        $data['columns'] = array(
            "Name"=>"name",
            "URL Match"=>"urlMatchExpression",
            "Last Modified"=>"modifiedAt",
            "Last Modified By"=>"modifiedBy",
        );
        $data['items'] = $this->getEntityManager()
            ->createQuery(
                'SELECT
                c.name,
                c.id,
                c.securekey,
                c.urlMatchExpression,
                c.modifiedAt,
                c.modifiedBy
                FROM JfxNinjaCMSBundle:Module c
                ORDER BY c.name ASC'
            )
            ->getResult();


        return $data;
    }




}
