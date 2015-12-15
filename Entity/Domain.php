<?php

namespace SSone\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Util\SecureRandom;

/**
 * @ORM\Entity(repositoryClass="SSone\CMSBundle\Entity\DomainsRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="ssone_domains")
 */
class Domain {

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
     * @ORM\Column(name="modifiedAt", type="datetime")
     */
    protected $modifiedAt;

    /**
     * @ORM\Column(name="domain", type="string", length=120)
     */
    private $domain;

    /**
     * @ORM\Column(name="metaDescription", type="array", length=255, nullable=true)
     */
    protected $metaDescription;

    /**
     * @ORM\Column(name="domainHTMLTemplate", type="string", length=120, nullable=true)
     */
    private $domainHTMLTemplate;

    /**
     * @ORM\Column(name="themeBundleName", type="string", length=120, nullable=true)
     */
    private $themeBundleName;

    /**
     * @ORM\OneToMany(targetEntity="Menu", mappedBy="domain")
     */
    private $menus;


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
     * Constructor
     */
    public function __construct()
    {
        $this->menus = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set createdBy
     *
     * @param string $createdBy
     * @return Domain
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
     * @return Domain
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
     * @return Domain
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
     * @return Domain
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
     * Set domain
     *
     * @param string $domain
     * @return Domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get domain
     *
     * @return string 
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set domainHTMLTemplate
     *
     * @param string $domainHTMLTemplate
     * @return Domain
     */
    public function setDomainHTMLTemplate($domainHTMLTemplate)
    {
        $this->domainHTMLTemplate = $domainHTMLTemplate;

        return $this;
    }

    /**
     * Get domainHTMLTemplate
     *
     * @return string 
     */
    public function getDomainHTMLTemplate()
    {
        return $this->domainHTMLTemplate;
    }

    /**
     * Add menus
     *
     * @param \SSone\CMSBundle\Entity\Menu $menus
     * @return Domain
     */
    public function addMenu(\SSone\CMSBundle\Entity\Menu $menus)
    {
        $this->menus[] = $menus;

        return $this;
    }

    /**
     * Remove menus
     *
     * @param \SSone\CMSBundle\Entity\Menu $menus
     */
    public function removeMenu(\SSone\CMSBundle\Entity\Menu $menus)
    {
        $this->menus->removeElement($menus);
    }

    /**
     * Get menus
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMenus()
    {
        return $this->menus;
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
     * Set themeBundleName
     *
     * @param string $themeBundleName
     *
     * @return Domain
     */
    public function setThemeBundleName($themeBundleName)
    {
        $this->themeBundleName = $themeBundleName;

        return $this;
    }

    /**
     * Get themeBundleName
     *
     * @return string
     */
    public function getThemeBundleName()
    {
        return $this->themeBundleName;
    }
}
