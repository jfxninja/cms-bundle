<?php

namespace SSone\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Util\SecureRandom;

/**
 * @ORM\Entity(repositoryClass="SSone\CMSBundle\Entity\ContentTypesRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="ssone_contentTypes")
 */
class ContentType
{

    public function __construct()
    {
        $this->fields = new ArrayCollection();
    }

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
     * @ORM\Column(name="slug", type="array", length=120)
     */
    private $slug;

    /**
     * @ORM\Column(name="contentTemplatePath", type="string", length=120, nullable=true)
     */
    private $contentTemplatePath;

    /**
     * @ORM\Column(name="listTemplatePath", type="string", length=120, nullable=true)
     */
    private $listTemplatePath;

    /**
     * @ORM\Column(name="categoryPageTemplatePath", type="string", length=120, nullable=true)
     */
    private $categoryPageTemplatePath;

    /**
     * @ORM\Column(name="adminTemplatePath", type="string", length=120, nullable=true)
     */
    private $adminTemplatePath;


    /**
     * @ORM\Column(name="hideFromMenus", type="boolean", nullable=true)
     */
    private $hideFromMenus;

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
     * @ORM\OneToMany(targetEntity="Field", mappedBy="contentTypeByVariable", cascade={"persist", "remove"})
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    private $variableFields;

    /**
     * @ORM\OneToMany(targetEntity="Field", mappedBy="contentTypeByAttribute", cascade={"persist", "remove"})
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    private $attributeFields;

    /**
     * @ORM\OneToMany(targetEntity="Content", mappedBy="contentType", cascade={"persist", "remove"})
     */
    private $content;

    /**
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="contentType", cascade={"persist", "remove"})
     */
    private $menuItems;


    /**
     * @ORM\OneToMany(targetEntity="Block", mappedBy="contentType", cascade={"persist", "remove"})
     */
    private $blocks;


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
     * Set name
     *
     * @param string $name
     * @return ContentType
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
     * Set securekey
     *
     * @param string $securekey
     * @return ContentType
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
     * Set createdBy
     *
     * @param string $createdBy
     * @return ContentType
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
     * @return ContentType
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
     * @return ContentType
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
     * @return ContentType
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
     * Set adminTemplatePath
     *
     * @param string $adminTemplatePath
     * @return ContentType
     */
    public function setAdminTemplatePath($adminTemplatePath)
    {
        $this->adminTemplatePath = $adminTemplatePath;

        return $this;
    }

    /**
     * Get adminTemplatePath
     *
     * @return string 
     */
    public function getAdminTemplatePath()
    {
        return $this->adminTemplatePath;
    }

    /**
     * Set contentTemplatePath
     *
     * @param string $contentTemplatePath
     * @return ContentType
     */
    public function setContentTemplatePath($contentTemplatePath)
    {
        $this->contentTemplatePath = $contentTemplatePath;

        return $this;
    }

    /**
     * Get contentTemplatePath
     *
     * @return string 
     */
    public function getContentTemplatePath()
    {
        return $this->contentTemplatePath;
    }

    /**
     * Set listTemplatePath
     *
     * @param string $listTemplatePath
     * @return ContentType
     */
    public function setListTemplatePath($listTemplatePath)
    {
        $this->listTemplatePath = $listTemplatePath;

        return $this;
    }

    /**
     * Get listTemplatePath
     *
     * @return string 
     */
    public function getListTemplatePath()
    {
        return $this->listTemplatePath;
    }

    /**
     * Set categoryPageTemplatePath
     *
     * @param string $categoryPageTemplatePath
     * @return ContentType
     */
    public function setCategoryPageTemplatePath($categoryPageTemplatePath)
    {
        $this->categoryPageTemplatePath = $categoryPageTemplatePath;

        return $this;
    }

    /**
     * Get categoryPageTemplatePath
     *
     * @return string 
     */
    public function getCategoryPageTemplatePath()
    {
        return $this->categoryPageTemplatePath;
    }


    /**
     * Add menuItems
     *
     * @param \SSone\CMSBundle\Entity\MenuItem $menuItems
     * @return ContentType
     */
    public function addMenuItem(\SSone\CMSBundle\Entity\MenuItem $menuItems)
    {
        $this->menuItems[] = $menuItems;

        return $this;
    }

    /**
     * Remove menuItems
     *
     * @param \SSone\CMSBundle\Entity\MenuItem $menuItems
     */
    public function removeMenuItem(\SSone\CMSBundle\Entity\MenuItem $menuItems)
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
     * Add content
     *
     * @param \SSone\CMSBundle\Entity\Content $content
     * @return ContentType
     */
    public function addContent(\SSone\CMSBundle\Entity\Content $content)
    {
        $this->content[] = $content;

        return $this;
    }

    /**
     * Remove content
     *
     * @param \SSone\CMSBundle\Entity\Content $content
     */
    public function removeContent(\SSone\CMSBundle\Entity\Content $content)
    {
        $this->content->removeElement($content);
    }

    /**
     * Get content
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContent()
    {
        return $this->content;
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
     * @param $locale
     * @return array 
     */
    public function getSlug($locale = null)
    {
        $s = $this->slug;

        if($locale && isset($s[$locale]))
        {
            $s = $s[$locale];
        }

        return $s;
    }

    /**
     * Add variableFields
     *
     * @param \SSone\CMSBundle\Entity\Field $variableField
     * @return ContentType
     */
    public function addVariableField(\SSone\CMSBundle\Entity\Field $variableField)
    {
        $variableField->setContentTypeByVariable($this);

        $this->variableFields[] = $variableField;

        return $this;
    }

    /**
     * Remove variableFields
     *
     * @param \SSone\CMSBundle\Entity\Field $variableFields
     */
    public function removeVariableField(\SSone\CMSBundle\Entity\Field $variableFields)
    {
        $this->variableFields->removeElement($variableFields);
    }

    /**
     * Get variableFields
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVariableFields()
    {
        return $this->variableFields;
    }

    /**
     * Add attributeFields
     *
     * @param \SSone\CMSBundle\Entity\Field $attributeField
     * @return ContentType
     */
    public function addAttributeField(\SSone\CMSBundle\Entity\Field $attributeField)
    {
        $attributeField->setContentTypeByAttribute($this);

        $this->attributeFields[] = $attributeField;

        return $this;
    }

    /**
     * Remove attributeFields
     *
     * @param \SSone\CMSBundle\Entity\Field $attributeFields
     */
    public function removeAttributeField(\SSone\CMSBundle\Entity\Field $attributeFields)
    {
        $this->attributeFields->removeElement($attributeFields);
    }

    /**
     * Get attributeFields
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAttributeFields()
    {
        return $this->attributeFields;
    }

    /**
     * Add blocks
     *
     * @param \SSone\CMSBundle\Entity\Block $blocks
     * @return ContentType
     */
    public function addBlock(\SSone\CMSBundle\Entity\Block $blocks)
    {
        $this->blocks[] = $blocks;

        return $this;
    }

    /**
     * Remove blocks
     *
     * @param \SSone\CMSBundle\Entity\Block $blocks
     */
    public function removeBlock(\SSone\CMSBundle\Entity\Block $blocks)
    {
        $this->blocks->removeElement($blocks);
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
     * Set hideFromMenus
     *
     * @param boolean $hideFromMenus
     * @return ContentType
     */
    public function setHideFromMenus($hideFromMenus)
    {
        $this->hideFromMenus = $hideFromMenus;

        return $this;
    }

    /**
     * Get hideFromMenus
     *
     * @return boolean 
     */
    public function getHideFromMenus()
    {
        return $this->hideFromMenus;
    }
}
