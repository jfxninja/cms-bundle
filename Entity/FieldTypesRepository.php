<?php

namespace SSone\CMSBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * FieldTypesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FieldTypesRepository extends EntityRepository
{

    public function findBySecurekey($securekey)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT c FROM SSoneCMSBundle:FieldType c WHERE c.securekey = :securekey'
            )->setParameter('securekey', $securekey)
            ->getSingleResult();
    }


    public function getItemsForListTable()
    {
        $data['type'] = "Field Types";
        $data['columns'] = array(
            "Name"=>"name",
            "Variable Name"=>"variableName",
            "ID"=>"id"
        );
        $data['items'] = $this->getEntityManager()
            ->createQuery(
                'SELECT c.name, c.id, c.variableName, c.securekey FROM SSoneCMSBundle:FieldType c ORDER BY c.name ASC'
            )
            ->getResult();
        return $data;
    }
}