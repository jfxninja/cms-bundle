<?php

namespace SSone\CMSBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * FieldTypesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FieldsRepository extends EntityRepository
{

    public function findBySecurekey($securekey)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT c FROM SSoneCMSBundle:Field c WHERE c.securekey = :securekey'
            )->setParameter('securekey', $securekey)
            ->getSingleResult();
    }

    public function fieldSettingsById($id)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT f.id, f.fieldTypeSettings
                FROM SSoneCMSBundle:Field f
                WHERE f.id = :id'
            )->setParameter('id', $id)
            ->getSingleResult();
    }


    public function getItemsForListTable()
    {
        $data['type'] = "Content Type Fields";
        $data['columns'] = array(
            "Name"=>"name",
            "ID"=>"id"
        );
        $data['items'] = $this->getEntityManager()
            ->createQuery(
                'SELECT c.name, c.id, c.securekey FROM SSoneCMSBundle:Field c ORDER BY c.name ASC'
            )
            ->getResult();
        return $data;
    }

    public function findByContentTypeId($id)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT f
                FROM SSoneCMSBundle:Field f
                LEFT JOIN f.contentTypeByVariable ct
                WHERE ct.id = :id'
            )->setParameter('id', $id)
            ->getResult();
    }

    public function getContentTypeCategoryFields($id)
    {

        $fieldTypes = $this->getAllowedCategoryFilterFieldTypes();

        return $this->getEntityManager()
            ->createQuery(
                'SELECT f.id, f.name
                FROM SSoneCMSBundle:Field f
                LEFT JOIN f.contentTypeByVariable ct
                LEFT JOIN f.fieldType ft
                WHERE ct.id = :id
                AND ft.variableName IN (:fieldTypes)'
            )
            ->setParameter('fieldTypes', $fieldTypes)
            ->setParameter('id', $id)
            ->getResult();
    }

    public function getContentTypeCategoryFieldsAsObjects($id)
    {

        $fieldTypes = $this->getAllowedCategoryFilterFieldTypes();

        return $this->getEntityManager()
            ->createQuery(
                'SELECT f
                FROM SSoneCMSBundle:Field f
                LEFT JOIN f.contentTypeByVariable ct
                LEFT JOIN f.fieldType ft
                WHERE ct.id = :id
                AND ft.variableName IN (:fieldTypes)'
            )
            ->setParameter('fieldTypes', $fieldTypes)
            ->setParameter('id', $id)
            ->getResult();
    }

    private function getAllowedCategoryFilterFieldTypes()
    {
        return array("choice","relatedcontent");

    }



    public function getContentTypeFields($contentTypeId, $fieldTypes)
    {

        $fields = array();

        $results = $this->getEntityManager()
            ->createQuery(
                'SELECT f.id, f.name
                FROM SSoneCMSBundle:Field f INDEX BY f.id
                LEFT JOIN f.contentTypeByVariable ct
                LEFT JOIN f.fieldType ft
                WHERE ct.id = :id
                AND ft.variableName IN (:fieldTypes)'
            )
            ->setParameter('fieldTypes', $fieldTypes)
            ->setParameter('id', $contentTypeId)
            ->getResult();

        foreach($results as $k=>$v)
        {
            $fields[$v['id']] = $v['name'];
        }

        return $fields;
    }


}
