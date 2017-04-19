<?php

namespace JfxNinja\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Util\SecureRandom;

/**
 * @ORM\Entity(repositoryClass="JfxNinja\CMSBundle\Entity\menuItemsRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="ninjacms_menuItems")
 */
class MenuItem {

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
     * @ORM\Column(name="name", type="array")
     */
    private $name;

    /**
     * @ORM\Column(name="createdBy", type="string", length=120)
     */
    private $createdBy;

    /**
     * @ORM\Column(name="domain_template_override", type="string", nullable=true)
     */
    private $domain_template_override;

    /**
     * @ORM\Column(name="hide", type="boolean", nullable=true)
     */
    private $hide;

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
     * @ORM\Column(name="sort", type="integer")
     */
    private $sort;

    /**
     * @ORM\ManyToOne(targetEntity="Menu", inversedBy="menuItems")
     * @ORM\JoinColumn(name="fk_rootMenu_id", referencedColumnName="id")
     */
    protected $root;

    /**
     * @ORM\ManyToOne(targetEntity="MenuItem", inversedBy="children")
     * @ORM\JoinColumn(name="fk_parentMenuItem_id", referencedColumnName="id")
     */
    protected $parent;

    /**
    * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="parent", cascade={"persist", "remove"})
     */
    protected $children;

    /**
     * @ORM\Column(name="mapAttached", type="string", length=120, nullable=true)
     */
    protected $mapAttached;

    /**
     * @ORM\ManyToOne(targetEntity="Content", inversedBy="menuItems")
     * @ORM\JoinColumn(name="fk_content_id", referencedColumnName="id")
     */
    protected $content;

    /**
     * @ORM\ManyToOne(targetEntity="ContentType", inversedBy="menuItems")
     * @ORM\JoinColumn(name="fk_contentType_id", referencedColumnName="id")
     */
    protected $contentType;

    /**
     * @ORM\Column(name="mapContent", type="string", length=120, nullable=true)
     */
    protected $mapContent;

    /**
     * @ORM\Column(name="mode", type="string", length=120)
     */
    protected $mode;

    /**
     * @ORM\Column(name="pageClass", type="string", length=120)
     */
    protected $pageClass;

    /**
     * @ORM\Column(name="pageTitle", type="array", length=120, nullable=true)
     */
    protected $pageTitle;

    /**
     * @ORM\Column(name="metaDescription", type="array", length=255, nullable=true)
     */
    protected $metaDescription;

    /**
     * @ORM\Column(name="slug", type="array")
     */
    protected $slug;

    /**
     * @ORM\Column(name="grandChildrenTemplatePosition", type="string", length=120)
     */
    protected $grandChildrenTemplatePosition;

    /**
     * @ORM\Column(name="grandChildrenRelativePosition", type="string", length=120)
     */
    protected $grandChildrenRelativePosition;

    /**
     * @ORM\Column(name="drawAllGrandChildren", type="boolean")
     */
    protected $drawAllGrandChildren;

    /**
     * @ORM\Column(name="hideEmptyCategories", type="boolean")
     */
    protected $hideEmptyCategories;

    /**
     * @ORM\Column(name="drawListItemsAsMenuItems", type="boolean")
     */
    protected $drawListItemsAsMenuItems;

    /**
     * @ORM\Column(name="pagination", type="integer",nullable=true)
     */
    private $pagination;

    /**
     * @ORM\Column(name="blogArchiveMenuPosition", type="string", length=120,nullable=true)
     */
    protected $blogArchiveMenuPosition;

    /**
     * @ORM\ManyToOne(targetEntity="Field")
     * @ORM\JoinColumn(name="fk_field_id_category1", referencedColumnName="id")
     */
    protected $contentCategory1;

    /**
     * @ORM\ManyToOne(targetEntity="Field")
     * @ORM\JoinColumn(name="fk_field_id_category2", referencedColumnName="id")
     */
    protected $contentCategory2;

    /**
     * @ORM\ManyToOne(targetEntity="Field")
     * @ORM\JoinColumn(name="fk_field_id_categoryRelationship", referencedColumnName="id")
     */
    protected $contentCategoryRelationship;



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
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set root
     *
     * @param \JfxNinja\CMSBundle\Entity\Menu $root
     * @return MenuItem
     */
    public function setRoot(\JfxNinja\CMSBundle\Entity\Menu $root = null)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * Get root
     *
     * @return \JfxNinja\CMSBundle\Entity\Menu
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Set parent
     *
     * @param \JfxNinja\CMSBundle\Entity\MenuItem $parent
     * @return MenuItem
     */
    public function setParent(\JfxNinja\CMSBundle\Entity\MenuItem $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \JfxNinja\CMSBundle\Entity\MenuItem
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param \JfxNinja\CMSBundle\Entity\MenuItem $children
     * @return MenuItem
     */
    public function addChild(\JfxNinja\CMSBundle\Entity\MenuItem $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \JfxNinja\CMSBundle\Entity\MenuItem $children
     */
    public function removeChild(\JfxNinja\CMSBundle\Entity\MenuItem $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set sort
     *
     * @param integer $sort
     * @return MenuItem
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
     * Set mapAttached
     *
     * @param string $mapAttached
     * @return MenuItem
     */
    public function setMapAttached($mapAttached)
    {
        $this->mapAttached = $mapAttached;

        return $this;
    }

    /**
     * Get mapAttached
     *
     * @return string 
     */
    public function getMapAttached()
    {
        return $this->mapAttached;
    }

    /**
     * Set mapContent
     *
     * @param string $mapContent
     * @return MenuItem
     */
    public function setMapContent($mapContent)
    {
        $this->mapContent = $mapContent;

        return $this;
    }

    /**
     * Get mapContent
     *
     * @return string 
     */
    public function getMapContent()
    {
        return $this->mapContent;
    }

    /**
     * Set mode
     *
     * @param string $mode
     * @return MenuItem
     */
    public function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * Get mode
     *
     * @return string 
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Set content
     *
     * @param \JfxNinja\CMSBundle\Entity\Content $content
     * @return MenuItem
     */
    public function setContent(\JfxNinja\CMSBundle\Entity\Content $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return \JfxNinja\CMSBundle\Entity\Content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set contentType
     *
     * @param \JfxNinja\CMSBundle\Entity\ContentType $contentType
     * @return MenuItem
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
     * Set drawAllGrandChildren
     *
     * @param string $drawAllGrandChildren
     * @return MenuItem
     */
    public function setDrawAllGrandChildren($drawAllGrandChildren)
    {
        $this->drawAllGrandChildren = $drawAllGrandChildren;

        return $this;
    }

    /**
     * Get drawAllGrandChildren
     *
     * @return string 
     */
    public function getDrawAllGrandChildren()
    {
        return $this->drawAllGrandChildren;
    }

    /**
     * Set grandChildrenRelativePosition
     *
     * @param string $grandChildrenRelativePosition
     * @return MenuItem
     */
    public function setGrandChildrenRelativePosition($grandChildrenRelativePosition)
    {
        $this->grandChildrenRelativePosition = $grandChildrenRelativePosition;

        return $this;
    }

    /**
     * Get grandChildrenRelativePosition
     *
     * @return string 
     */
    public function getGrandChildrenRelativePosition()
    {
        return $this->grandChildrenRelativePosition;
    }

    /**
     * Set grandChildrenTemplatePosition
     *
     * @param string $grandChildrenTemplatePosition
     * @return MenuItem
     */
    public function setGrandChildrenTemplatePosition($grandChildrenTemplatePosition)
    {
        $this->grandChildrenTemplatePosition = $grandChildrenTemplatePosition;

        return $this;
    }

    /**
     * Get grandChildrenTemplatePosition
     *
     * @return string 
     */
    public function getGrandChildrenTemplatePosition()
    {
        return $this->grandChildrenTemplatePosition;
    }

    /**
     * Set name
     *
     * @param array $name
     * @return MenuItem
     */
    public function setName($name)
    {
        $n = $this->getName();
        foreach($name as $k=>$v)
        {
            $n[$k] = $v;
        }
        $this->name = $n;

        return $this;
    }

    /**
     * Get name
     *
     * @param $locale
     * @return array 
     */
    public function getName($locale = "")
    {
        $n = $this->name;

        if($locale && isset($n[$locale]))
        {
            $n = $n[$locale];
        }

        return $n;
    }

    /**
     * Set slug
     *
     * @param array $slug
     * @return MenuItem
     */
    public function setSlug($slug)
    {
        $s = $this->getName();
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
     * @return array 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set pagination
     *
     * @param integer $pagination
     * @return MenuItem
     */
    public function setPagination($pagination)
    {
        $this->pagination = $pagination;

        return $this;
    }

    /**
     * Get pagination
     *
     * @return integer 
     */
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * Set blogArchiveMenuPosition
     *
     * @param string $blogArchiveMenuPosition
     * @return MenuItem
     */
    public function setBlogArchiveMenuPosition($blogArchiveMenuPosition)
    {
        $this->blogArchiveMenuPosition = $blogArchiveMenuPosition;

        return $this;
    }

    /**
     * Get blogArchiveMenuPosition
     *
     * @return string 
     */
    public function getBlogArchiveMenuPosition()
    {
        return $this->blogArchiveMenuPosition;
    }

    /**
     * Set contentCategory1
     *
     * @param \JfxNinja\CMSBundle\Entity\Field $contentCategory1
     * @return MenuItem
     */
    public function setContentCategory1(\JfxNinja\CMSBundle\Entity\Field $contentCategory1 = null)
    {
        $this->contentCategory1 = $contentCategory1;

        return $this;
    }

    /**
     * Get contentCategory1
     *
     * @return \JfxNinja\CMSBundle\Entity\Field
     */
    public function getContentCategory1()
    {
        return $this->contentCategory1;
    }

    /**
     * Set contentCategory2
     *
     * @param \JfxNinja\CMSBundle\Entity\Field $contentCategory2
     * @return MenuItem
     */
    public function setContentCategory2(\JfxNinja\CMSBundle\Entity\Field $contentCategory2 = null)
    {
        $this->contentCategory2 = $contentCategory2;

        return $this;
    }

    /**
     * Get contentCategory2
     *
     * @return \JfxNinja\CMSBundle\Entity\Field
     */
    public function getContentCategory2()
    {
        return $this->contentCategory2;
    }

    /**
     * Set contentCategoryRelationship
     *
     * @param \JfxNinja\CMSBundle\Entity\Field $contentCategoryRelationship
     * @return MenuItem
     */
    public function setContentCategoryRelationship(\JfxNinja\CMSBundle\Entity\Field $contentCategoryRelationship = null)
    {
        $this->contentCategoryRelationship = $contentCategoryRelationship;

        return $this;
    }

    /**
     * Get contentCategoryRelationship
     *
     * @return \JfxNinja\CMSBundle\Entity\Field
     */
    public function getContentCategoryRelationship()
    {
        return $this->contentCategoryRelationship;
    }

    /**
     * Set hideEmptyCategories
     *
     * @param boolean $hideEmptyCategories
     * @return MenuItem
     */
    public function setHideEmptyCategories($hideEmptyCategories)
    {
        $this->hideEmptyCategories = $hideEmptyCategories;

        return $this;
    }

    /**
     * Get hideEmptyCategories
     *
     * @return boolean 
     */
    public function getHideEmptyCategories()
    {
        return $this->hideEmptyCategories;
    }

    /**
     * Set drawListItemsAsMenuItems
     *
     * @param boolean $drawListItemsAsMenuItems
     * @return MenuItem
     */
    public function setDrawListItemsAsMenuItems($drawListItemsAsMenuItems)
    {
        $this->drawListItemsAsMenuItems = $drawListItemsAsMenuItems;

        return $this;
    }

    /**
     * Get drawListItemsAsMenuItems
     *
     * @return boolean 
     */
    public function getDrawListItemsAsMenuItems()
    {
        return $this->drawListItemsAsMenuItems;
    }

    /**
     * Set pageTitle
     *
     * @param array $pageTitle
     * @return MenuItem
     */
    public function setPageTitle($pageTitle)
    {
        $s = $this->getPageTitle();
        foreach($pageTitle as $k=>$v)
        {
            $s[$k] = $v;
        }
        $this->pageTitle = $s;

        return $this;
    }

    /**
     * Get pageTitle
     *
     * @param string $locale
     * @return string 
     */
    public function getPageTitle($locale="")
    {
        $n = $this->pageTitle;

        if($locale && isset($n[$locale]))
        {
            $n = $n[$locale];
        }

        return $n;
    }

    /**
     * Set metaDescription
     *
     * @param array $metaDescription
     * @return MenuItem
     */
    public function setMetaDescription($metaDescription)
    {
        $s = $this->getMetaDescription();
        foreach($metaDescription as $k=>$v)
        {
            $s[$k] = $v;
        }
        $this->metaDescription = $s;

        return $this;
    }

    /**
     * Get metaDescription
     * @param string $locale
     * @return string 
     */
    public function getMetaDescription($locale="")
    {
        $n = $this->metaDescription;

        if($locale && isset($n[$locale]))
        {
            $n = $n[$locale];
        }

        return $n;
    }

    /**
     * Set pageClass
     *
     * @param string $pageClass
     * @return MenuItem
     */
    public function setPageClass($pageClass)
    {
        $this->pageClass = $pageClass;

        return $this;
    }

    /**
     * Get pageClass
     *
     * @return string 
     */
    public function getPageClass()
    {
        return $this->pageClass;
    }

    /**
     * Set domainTemplateOverride
     *
     * @param string $domainTemplateOverride
     *
     * @return MenuItem
     */
    public function setDomainTemplateOverride($domainTemplateOverride)
    {
        $this->domain_template_override = $domainTemplateOverride;

        return $this;
    }

    /**
     * Get domainTemplateOverride
     *
     * @return string
     */
    public function getDomainTemplateOverride()
    {
        return $this->domain_template_override;
    }

    /**
     * Set hide
     *
     * @param boolean $hide
     *
     * @return MenuItem
     */
    public function setHide($hide)
    {
        $this->hide = $hide;

        return $this;
    }

    /**
     * Get hide
     *
     * @return boolean
     */
    public function getHide()
    {
        return $this->hide;
    }
}
