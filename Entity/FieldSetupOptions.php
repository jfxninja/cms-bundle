<?php

namespace SSone\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Util\SecureRandom;

/**
 * @ORM\Entity(repositoryClass="SSone\CMSBundle\Entity\FieldSetupOptionsRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="ssone_fieldSetupOptions")
 */
class FieldSetupOptions {

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="securekey", type="string", length=32)
     */
    private $securekey;

    /**
     * @ORM\Column(name="name", type="string", length=120)
     */
    private $name;

    /**
     * @ORM\Column(name="variableName", type="string", length=120, unique=true)
     */
    private $variableName;

    /**
     * @ORM\Column(name="createdBy", type="string", length=120)
     */
    private $createdBy;

    /**
     * @ORM\Column(name="modifiedBy", type="string", length=120)
     */
    private $modifiedBy;

    /**
     * @ORM\Column(name="createdAt", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="modifiedAt", type="datetime")
     */
    protected $modifiedAt;

    /**
     * @ORM\Column(name="label", type="string", length=120)
     */
    private $label;

    /**
     * @ORM\Column(name="fieldVar", type="string", length=120, nullable=true)
     */
    private $fieldVar;

    /**
    * @ORM\Column(name="inputType", type="string", length=120)
    */
    private $inputType;

    /**
     * @ORM\Column(name="inputTypeVar", type="string", length=120, nullable=true)
     */
    private $inputTypeVar;

    /**
     * @ORM\ManyToOne(targetEntity="FieldType",  inversedBy="fieldSetupOptions" )
     * @ORM\JoinColumn(name="fk_fieldType_id", referencedColumnName="id")
     */
    protected $fieldType;

    /**
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamps()
    {
        $this->setModifiedAt(new \DateTime(date('Y-m-d H:i:s')));

        if($this->getCreatedAt() == null)
        {
            $this->setCreatedAt(new \DateTime(date('Y-m-d H:i:s')));
        }
    }

    /**
     *
     * @ORM\PrePersist
     */
    public function generateSecurekey()
    {
        $generator = new SecureRandom();
        $random = $generator->nextBytes(150);
        $securekey = md5($random . time());
        $this->setSecurekey($securekey);
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set securekey
     *
     * @param string $securekey
     * @return Field
     */
    public function setSecurekey($securekey)
    {
        $this->securekey = $securekey;

        return $this;
    }

    /**
     * Get securekey
     *
     * @return string 
     */
    public function getSecurekey()
    {
        return $this->securekey;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Field
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set createdBy
     *
     * @param string $createdBy
     * @return Field
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return string 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set modifiedBy
     *
     * @param string $modifiedBy
     * @return Field
     */
    public function setModifiedBy($modifiedBy)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    /**
     * Get modifiedBy
     *
     * @return string 
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Field
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set modifiedAt
     *
     * @param \DateTime $modifiedAt
     * @return Field
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * Get modifiedAt
     *
     * @return \DateTime 
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * Set label
     *
     * @param string $label
     * @return Field
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }



    /**
     * Set inputType
     *
     * @param string $inputType
     * @return FieldSetupOptions
     */
    public function setInputType($inputType)
    {
        $this->inputType = $inputType;

        return $this;
    }

    /**
     * Get inputType
     *
     * @return string 
     */
    public function getInputType()
    {
        return $this->inputType;
    }



    /**
     * Set fieldType
     *
     * @param \SSone\CMSBundle\Entity\FieldType $fieldType
     * @return FieldSetupOptions
     */
    public function setFieldType(\SSone\CMSBundle\Entity\FieldType $fieldType = null)
    {
        $this->fieldType = $fieldType;

        return $this;
    }

    /**
     * Get fieldType
     *
     * @return \SSone\CMSBundle\Entity\FieldType
     */
    public function getFieldType()
    {
        return $this->fieldType;
    }

    /**
     * Set variableName
     *
     * @param string $variableName
     * @return FieldSetupOptions
     */
    public function setVariableName($variableName)
    {
        $this->variableName = $variableName;

        return $this;
    }

    /**
     * Get variableName
     *
     * @return string 
     */
    public function getVariableName()
    {
        return $this->variableName;
    }

    /**
     * Set inputTypeVar
     *
     * @param string $inputTypeVar
     * @return FieldSetupOptions
     */
    public function setInputTypeVar($inputTypeVar)
    {
        $this->inputTypeVar = $inputTypeVar;

        return $this;
    }

    /**
     * Get inputTypeVar
     *
     * @return string 
     */
    public function getInputTypeVar()
    {
        return $this->inputTypeVar;
    }

    /**
     * Set fieldVar
     *
     * @param string $fieldVar
     * @return FieldSetupOptions
     */
    public function setFieldVar($fieldVar)
    {
        $this->fieldVar = $fieldVar;

        return $this;
    }

    /**
     * Get fieldVar
     *
     * @return string
     */
    public function getFieldVar()
    {
        return $this->fieldVar;
    }
}
