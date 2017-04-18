<?php

namespace JfxNinja\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Util\SecureRandom;

/**
 * @ORM\Entity(repositoryClass="JfxNinja\CMSBundle\Entity\BlocksRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="ninjacms_blocks")
 */
class Block {

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
     * @ORM\Column(name="sort", type="integer")
     */
    private $sort;

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
     * @ORM\ManyToOne(targetEntity="Field", inversedBy="blocks" )
     * @ORM\JoinColumn(name="fk_field_id", referencedColumnName="id")
     */
    protected $field;

    /**
     * @ORM\ManyToOne(targetEntity="Content", inversedBy="blocks")
     * @ORM\JoinColumn(name="fk_content_id", referencedColumnName="id")
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="ContentType", inversedBy="blocks")
     * @ORM\JoinColumn(name="fk_contentType_id", referencedColumnName="id")
     */
    private $contentType;


    /**
     * @ORM\OneToMany(targetEntity="BlockField",  mappedBy="block", cascade={"persist", "remove"})
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    protected $blockFields;

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
     * @return Block
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
     * @return Block
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
     * @return Block
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
     * @return Block
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
     * @return Block
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
     * @return Block
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
     * Set field
     *
     * @param \jfxninja\CMSBundle\Entity\field $field
     * @return Block
     */
    public function setField(\jfxninja\CMSBundle\Entity\field $field = null)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Get field
     *
     * @return \jfxninja\CMSBundle\Entity\field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set contentType
     *
     * @param \jfxninja\CMSBundle\Entity\ContentType $contentType
     * @return Block
     */
    public function setContentType(\jfxninja\CMSBundle\Entity\ContentType $contentType = null)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * Get contentType
     *
     * @return \jfxninja\CMSBundle\Entity\ContentType
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->blockFields = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add blockField
     *
     * @param \jfxninja\CMSBundle\Entity\BlockField $blockField
     * @return Block
     */
    public function addBlockField(\jfxninja\CMSBundle\Entity\BlockField $blockField)
    {

        $blockField->setBlock($this);

        $this->blockFields[] = $blockField;

        return $this;
    }

    /**
     * Remove blockField
     *
     * @param \jfxninja\CMSBundle\Entity\BlockField $blockFields
     */
    public function removeBlockField(\jfxninja\CMSBundle\Entity\BlockField $blockFields)
    {
        $this->blockFields->removeElement($blockFields);
    }

    /**
     * Get blockField
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBlockFields()
    {
        return $this->blockFields;
    }

    /**
     * Set sort
     *
     * @param integer $sort
     * @return Block
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort
     *
     * @return integer 
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Set content
     *
     * @param \jfxninja\CMSBundle\Entity\Content $content
     * @return Block
     */
    public function setContent(\jfxninja\CMSBundle\Entity\Content $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return \jfxninja\CMSBundle\Entity\Content
     */
    public function getContent()
    {
        return $this->content;
    }
}
