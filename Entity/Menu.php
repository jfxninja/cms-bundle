<?php

namespace SSone\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Util\SecureRandom;

/**
 * @ORM\Entity(repositoryClass="SSone\CMSBundle\Entity\menusRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="ssone_menus")
 */
class Menu {

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
     * @ORM\Column(type="datetime")
     */
    protected $modifiedAt;

    /**
     * @ORM\Column(name="sort", type="integer")
     */
    private $sort;

    /**
     * @ORM\ManyToOne(targetEntity="Domain", inversedBy="menus")
     * @ORM\JoinColumn(name="fk_domain_id", referencedColumnName="id")
     */
    private $domain;


    /**
     * @ORM\Column(name="menuTemplate", type="string", length=120, nullable=true)
     */
    private $menuTemplate;

    /**
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="root", cascade={"persist", "remove"})
     */
    private $menuItems;

    /**
     * @ORM\Column(name="menuTemplatePosition", type="string", length=120)
     */
    protected $menuTemplatePosition;

    /**
     * @ORM\Column(name="grandChildrenTemplatePosition", type="string", length=120)
     */
    protected $grandChildrenTemplatePosition;

    /**
     * @ORM\Column(name="drawAllGrandChildren", type="boolean")
     */
    protected $drawAllGrandChildren;

    /**
     * @ORM\Column(name="grandChildrenRelativePosition", type="string", length=120)
     */
    protected $grandChildrenRelativePosition;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->menuItems = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set sort
     *
     * @param integer $sort
     * @return Menu
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
     * Set menuTemplate
     *
     * @param string $menuTemplate
     * @return Menu
     */
    public function setMenuTemplate($menuTemplate)
    {
        $this->menuTemplate = $menuTemplate;

        return $this;
    }

    /**
     * Get menuTemplate
     *
     * @return string 
     */
    public function getMenuTemplate()
    {
        return $this->menuTemplate;
    }

    /**
     * Set domain
     *
     * @param \SSone\CMSBundle\Entity\Domain $domain
     * @return Menu
     */
    public function setDomain(\SSone\CMSBundle\Entity\Domain $domain = null)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get domain
     *
     * @return \SSone\CMSBundle\Entity\Domain
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Add menusItems
     *
     * @param \SSone\CMSBundle\Entity\MenuItem $menuItem
     * @return Menu
     */
    public function addMenusItem(\SSone\CMSBundle\Entity\MenuItem $menuItem)
    {
        $this->menuItems[] = $menuItem;

        return $this;
    }

    /**
     * Remove menusItems
     *
     * @param \SSone\CMSBundle\Entity\MenuItem $menuItem
     */
    public function removeMenusItem(\SSone\CMSBundle\Entity\MenuItem $menuItem)
    {
        $this->menuItems->removeElement($menuItem);
    }

    /**
     * Get menusItems
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMenusItems()
    {
        return $this->menuItems;
    }

    /**
     * Set drawAllGrandChildren
     *
     * @param string $drawAllGrandChildren
     * @return Menu
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
     * Add menuItems
     *
     * @param \SSone\CMSBundle\Entity\MenuItem $menuItems
     * @return Menu
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
     * Set grandChildrenRelativePosition
     *
     * @param string $grandChildrenRelativePosition
     * @return Menu
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
     * @return Menu
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
     * Set menuTemplatePosition
     *
     * @param string $menuTemplatePosition
     * @return Menu
     */
    public function setMenuTemplatePosition($menuTemplatePosition)
    {
        $this->menuTemplatePosition = $menuTemplatePosition;

        return $this;
    }

    /**
     * Get menuTemplatePosition
     *
     * @return string 
     */
    public function getMenuTemplatePosition()
    {
        return $this->menuTemplatePosition;
    }
}
