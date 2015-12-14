<?php

namespace SSone\CMSBundle\Services;

use Symfony\Component\Security\Core\SecurityContext;

/**
 * A simple service to audit record creation and modification with user and timestamp
 */
class RecordAuditor
{

    private $context;

    public function __construct(SecurityContext $context)
    {
        $this->context = $context;
    }

    /**
     * @param $record
     */
    public function auditRecord($record)
    {
        $this->audit($record, $this->getUserName());
    }

    /**
     * @return mixed
     */
    private function getUserName()
    {
        if($this->context->getToken())
        {
            return $this->context->getToken()->getUser()->getUsername();
        }
        else
        {
            return "System";
        }

    }


    /**
     * @param $record
     * @param $username
     */
    private function audit($record, $username)
    {
        if($record->getCreatedBy() == "") $record->setCreatedBy($username);
        $record->setModifiedBy($username);

    }

}