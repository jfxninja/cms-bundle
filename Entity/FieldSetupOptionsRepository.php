<?php

namespace SSone\CMSBundle\Entity;

use Doctrine\ORM\EntityRepository;


class FieldSetupOptionsRepository extends EntityRepository
{


    public function findBySecurekey($securekey)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT c FROM SSoneCMSBundle:FieldSetupOptions c WHERE c.securekey = :securekey'
            )->setParameter('securekey', $securekey)
            ->getSingleResult();
    }


    public function getItemsForListTable()
    {
        $data['type'] = "Field Setup Options";
        $data['columns'] = array(
            "Option Name"=>"name",
            "Variable Name"=>"variableName",
            "Label"=>"label",
            "Input Type"=>"inputType",
            "Associated Field Type"=>"fieldType",
            "Input Variable"=>"inputTypeVar",
            "ID"=>"id"
        );

        $data['items'] = $this->getEntityManager()
            ->createQuery(
                'SELECT fs.name, fs.id, fs.securekey, fs.label, fs.variableName, fs.inputType, fs.inputTypeVar, ft.name AS fieldType
                FROM SSoneCMSBundle:FieldSetupOptions fs
                LEFT JOIN fs.fieldType ft
                ORDER BY ft.name ASC, fs.fieldVar ASC, fs.name ASC'
            )
            ->getResult();

        return $data;
    }

    public function getAllFieldSetupOptions()
    {

        $data = $this->getEntityManager()
            ->createQuery(
                'SELECT fs.name, fs.id, fs.label, fs.variableName, fs.inputType, fs.inputTypeVar, ft.name AS fieldType, ft.id AS fieldTypeId, ft.variableName as fieldTypeVariableName
                FROM SSoneCMSBundle:FieldSetupOptions fs
                LEFT JOIN fs.fieldType ft
                ORDER BY ft.name ASC, fs.fieldVar ASC, fs.name ASC'
            )
            ->getResult();

        $options = array();
        $entityItems = array();


        foreach($data as $option)
        {
            $options[$option['fieldTypeId']]['options'][$option['variableName']] = $option;
            $options[$option['fieldTypeId']]['name'] = $option['fieldType'];
            $options[$option['fieldTypeId']]['fieldTypeVariableName'] = $option['fieldTypeVariableName'];

            if($option['inputType'] == "entity")
            {
                $entity = "SSoneCMSBundle:".$option['inputTypeVar'];
                $query = 'SELECT i.name, i.id FROM '.$entity.' i ORDER BY i.name ASC';
                $items = $this->getEntityManager()
                    ->createQuery($query)
                    ->getResult();
                $entityItems = array();
                foreach($items as $i)
                {
                    $entityItems[$i['id']] = $i['name'];
                }

                $options
                [$option['fieldTypeId']]
                ['options']
                [$option['variableName']]
                ['inputTypeVar'] = $entityItems;
            }

        }

        return $options;
    }
}
