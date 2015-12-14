<?php

namespace SSone\CMSBundle\Entity;

use Doctrine\ORM\EntityRepository;


class menusRepository extends EntityRepository
{


    public function findBySecurekey($securekey)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT c FROM SSoneCMSBundle:Menu c WHERE c.securekey = :securekey'
            )->setParameter('securekey', $securekey)
            ->getSingleResult();
    }


    public function getItemsForListTable()
    {
        $data['type'] = "Field Types";
        $data['columns'] = array(
            "Name"=>"name",
            "Site"=>"domain",
            "Last Modified"=>"modifiedAt",
            "Last Modified By"=>"modifiedBy",
        );
        $data['items'] = $this->getEntityManager()
            ->createQuery(
                'SELECT
                c.name,
                c.id,
                c.securekey,
                c.modifiedAt,
                c.modifiedBy,
                d.name AS domain
                FROM SSoneCMSBundle:Menu c
                LEFT JOIN c.domain d
                ORDER BY d.name, c.name ASC'
            )
            ->getResult();

        return $data;
    }


}
