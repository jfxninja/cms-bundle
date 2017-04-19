<?php

namespace JfxNinja\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Util\SecureRandom;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="JfxNinja\CMSBundle\Entity\FieldTypesRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="ninjacms_fieldTypes")
 */
class FieldType {

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
     * @ORM\Column(name="variableName", type="string", length=120)
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
     * @ORM\Column(name="label", type="string", length=120, nullable=true)
     */
    private $label;

    /**
     * @ORM\OneToMany(targetEntity="FieldSetupOptions", mappedBy="fieldType")
     */
    protected $fieldSetupOptions;

    /**
     * @ORM\OneToMany(targetEntity="Field", mappedBy="fieldType")
     */
    protected $fields;

   
    /**
     * Now we tell doctrine that before we persist or update we call the updatedTimestamps() function.
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
     * Constructor
     */
    public function __construct()
    {
        $this->fieldContentOptions = new ArrayCollection();
        $this->fieldSetupOptions = new ArrayCollection();
    }

    /**
     * Add fieldSetupOptions
     *
     * @param \JfxNinja\CMSBundle\Entity\FieldSetupOptions $fieldSetupOptions
     * @return FieldType
     */
    public function addFieldSetupOption(FieldSetupOptions $fieldSetupOptions)
    {
        $this->fieldSetupOptions[] = $fieldSetupOptions;

        return $this;
    }

    /**
     * Remove fieldSetupOptions
     *
     * @param \JfxNinja\CMSBundle\Entity\FieldSetupOptions $fieldSetupOptions
     */
    public function removeFieldSetupOption(FieldSetupOptions $fieldSetupOptions)
    {
        $this->fieldSetupOptions->removeElement($fieldSetupOptions);
    }

    /**
     * Get fieldSetupOptions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFieldSetupOptions()
    {
        return $this->fieldSetupOptions;
    }

    /**
     * Add fields
     *
     * @param \JfxNinja\CMSBundle\Entity\Field $fields
     * @return FieldType
     */
    public function addField(\JfxNinja\CMSBundle\Entity\Field $fields)
    {
        $this->fields[] = $fields;

        return $this;
    }

    /**
     * Remove fields
     *
     * @param \JfxNinja\CMSBundle\Entity\Field $fields
     */
    public function removeField(\JfxNinja\CMSBundle\Entity\Field $fields)
    {
        $this->fields->removeElement($fields);
    }

    /**
     * Get fields
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Set variableName
     *
     * @param string $variableName
     * @return FieldType
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
}
