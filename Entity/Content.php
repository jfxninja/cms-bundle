<?php

namespace JfxNinja\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Util\SecureRandom;

/**
 * @ORM\Entity(repositoryClass="JfxNinja\CMSBundle\Entity\ContentRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="ninjacms_content")
 */
class Content
{

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
     * @ORM\Column(name="name", type="string", length=120,nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(name="slug", type="array", length=120,nullable=true)
     */
    private $slug;

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
     * @ORM\Column(name="content", type="array", nullable=true)
     */
    private $content;


    /**
     * @ORM\Column(name="modifiedAt", type="datetime")
     */
    protected $modifiedAt;

    /**
     * @ORM\ManyToOne(targetEntity="ContentType", inversedBy="content")
     * @ORM\JoinColumn(name="fk_fieldContentType_id", referencedColumnName="id")
     */
    private $contentType;

    /**
     * @ORM\OneToMany(targetEntity="Block", mappedBy="content", cascade={"persist", "remove"})
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    private $blocks;

    /**
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="content", cascade={"persist", "remove"})
     */
    private $menuItems;



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
     * Constructor
     */
    public function __construct()
    {
        $this->blocks = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Content
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
     * @return Content
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
     * @return Content
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
     * @return Content
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
     * @return Content
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
     * @return Content
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
     * Set contentType
     *
     * @param \JfxNinja\CMSBundle\Entity\ContentType $contentType
     * @return Content
     */
    public function setContentType(\JfxNinja\CMSBundle\Entity\ContentType $contentType = null)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * Get contentType
     *
     * @return \JfxNinja\CMSBundle\Entity\ContentType
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Set content
     *
     * @param array $content
     * @return Content
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return array 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Add blocks
     *
     * @param \JfxNinja\CMSBundle\Entity\Block $block
     * @return Content
     */
    public function addBlock(\JfxNinja\CMSBundle\Entity\Block $block)
    {
        $this->blocks[] = $block;

        return $this;
    }

    /**
     * Remove blocks
     *
     * @param \JfxNinja\CMSBundle\Entity\Block $block
     */
    public function removeBlock(\JfxNinja\CMSBundle\Entity\Block $block)
    {
        $this->blocks->removeElement($block);
    }

    /**
     * Get blocks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBlocks()
    {
        return $this->blocks;
    }


    /**
     * Add menuItems
     *
     * @param \JfxNinja\CMSBundle\Entity\MenuItem $menuItems
     * @return Content
     */
    public function addMenuItem(\JfxNinja\CMSBundle\Entity\MenuItem $menuItems)
    {
        $this->menuItems[] = $menuItems;

        return $this;
    }

    /**
     * Remove menuItems
     *
     * @param \JfxNinja\CMSBundle\Entity\MenuItem $menuItems
     */
    public function removeMenuItem(\JfxNinja\CMSBundle\Entity\MenuItem $menuItems)
    {
        $this->menuItems->removeElement($menuItems);
    }

    /**
     * Get menuItems
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMenuItems()
    {
        return $this->menuItems;
    }

    /**
     * Set slug
     *
     * @param array $slug
     * @return Content
     */
    public function setSlug($slug)
    {
        $s = $this->getSlug();

        foreach($slug as $k=>$v)
        {
            $s[$k] = $v;
        }
        $this->slug = $s;

        return $this;

    }

    /**
     * Get slug
     *
     * @param $locale
     * @return array 
     */
    public function getSlug($locale = "")
    {
        $s = $this->slug;

        if($locale && isset($s[$locale]))
        {
            $s = $s[$locale];
        }

        return $s;
    }
}
