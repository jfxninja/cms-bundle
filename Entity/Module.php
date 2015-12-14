<?php

namespace SSone\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Util\SecureRandom;

/**
 * @ORM\Entity(repositoryClass="SSone\CMSBundle\Entity\modulesRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="ssone_modules")
 */
class Module {

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
     * @ORM\Column(name="name", type="text", length=120)
     */
    private $name;

    /**
     * @ORM\Column(name="title", type="array")
     */
    private $title;

    /**
     * @ORM\Column(name="description", type="array")
     */
    private $description;


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
     * @ORM\Column(name="resultLimit", type="integer",nullable=true)
     */
    private $resultsLimit;

    /**
     * @ORM\Column(name="urlMatchExpression", type="array",nullable=true)
     */
    protected $urlMatchExpression;

    /**
     * @ORM\Column(name="templatePosition", type="string", length=120,nullable=true)
     */
    protected $templatePosition;

    /**
     * @ORM\Column(name="templatePath", type="string", length=120,nullable=true)
     */
    protected $templatePath;

    /**
     * @ORM\Column(name="type", type="boolean", type="string", length=120)
     */
    protected $type;

    /**
     * @ORM\ManyToOne(targetEntity="ContentType")
     * @ORM\JoinColumn(name="fk_contentType_id", referencedColumnName="id")
     */
    protected $contentType;

    /**
     * @ORM\ManyToOne(targetEntity="Content")
     * @ORM\JoinColumn(name="fk_content_id", referencedColumnName="id")
     */
    protected $singleContentItem;

    /**
     * @ORM\ManyToOne(targetEntity="CMSForm")
     * @ORM\JoinColumn(name="fk_cmsform_id", referencedColumnName="id")
     */
    protected $form;

    /**
     * @ORM\Column(name="fk_field_id_filter", type="string", length=7,nullable=true)
     */
    protected $contentFilterField;

    /**
     * @ORM\Column(name="contentFilterValue", type="string", length=120,nullable=true)
     */
    private $contentFilterValue;

    /**
     * @ORM\Column(name="fk_field_id_orderBy", type="string", length=7,nullable=true)
     */
    protected $contentOrderByField;

    /**
     * @ORM\Column(name="contentOrderByValue", type="string", length=120,nullable=true)
     */
    private $contentOrderByValue;






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
     * Set name
     *
     * @param array $name
     * @return Module
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return array
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set sort
     *
     * @param integer $sort
     * @return Module
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
     * Set resultsLimit
     *
     * @param integer $resultsLimit
     * @return Module
     */
    public function setResultsLimit($resultsLimit)
    {
        $this->resultsLimit = $resultsLimit;

        return $this;
    }

    /**
     * Get resultsLimit
     *
     * @return integer
     */
    public function getResultsLimit()
    {
        return $this->resultsLimit;
    }

    /**
     * Set templatePosition
     *
     * @param string $templatePosition
     * @return Module
     */
    public function setTemplatePosition($templatePosition)
    {
        $this->templatePosition = $templatePosition;

        return $this;
    }

    /**
     * Get templatePosition
     *
     * @return string
     */
    public function getTemplatePosition()
    {
        return $this->templatePosition;
    }

    /**
     * Set templatePath
     *
     * @param string $templatePath
     * @return Module
     */
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;

        return $this;
    }

    /**
     * Get templatePath
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * Set contentFilterValue
     *
     * @param string $contentFilterValue
     * @return Module
     */
    public function setContentFilterValue($contentFilterValue)
    {
        $this->contentFilterValue = $contentFilterValue;

        return $this;
    }

    /**
     * Get contentFilterValue
     *
     * @return string
     */
    public function getContentFilterValue()
    {
        return $this->contentFilterValue;
    }

    /**
     * Set contentOrderByValue
     *
     * @param string $contentOrderByValue
     * @return Module
     */
    public function setContentOrderByValue($contentOrderByValue)
    {
        $this->contentOrderByValue = $contentOrderByValue;

        return $this;
    }

    /**
     * Get contentOrderByValue
     *
     * @return string
     */
    public function getContentOrderByValue()
    {
        return $this->contentOrderByValue;
    }

    /**
     * Set contentType
     *
     * @param \SSone\CMSBundle\Entity\ContentType $contentType
     * @return Module
     */
    public function setContentType(\SSone\CMSBundle\Entity\ContentType $contentType = null)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * Get contentType
     *
     * @return \SSone\CMSBundle\Entity\Content
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Set urlMatchExpression
     *
     * @param array $urlMatchExpression
     * @return Module
     */
    public function setUrlMatchExpression($urlMatchExpression)
    {
        $n = $this->getUrlMatchExpression();

        foreach($urlMatchExpression as $k=>$v)
        {
            $n[$k] = $v;
        }

        $this->urlMatchExpression = $n;

        return $this;
    }

    /**
     * Get urlMatchExpression
     *
     * @param $locale
     * @return array
     */
    public function getUrlMatchExpression($locale="")
    {
        $n = $this->urlMatchExpression;

        if($locale && isset($n[$locale]))
        {
            $n = $n[$locale];
        }

        return $n;
    }

    /**
     * Set title
     *
     * @param array $title
     * @return Module
     */
    public function setTitle($title)
    {
        $n = $this->getTitle();

        foreach($title as $k=>$v)
        {
            $n[$k] = $v;
        }

        $this->title = $n;

        return $this;
    }

    /**
     * Get title
     *
     * @param $locale
     * @return array
     */
    public function getTitle($locale="")
    {
        $n = $this->title;

        if($locale && isset($n[$locale]))
        {
            $n = $n[$locale];
        }

        return $n;
    }

    /**
     * Set description
     *
     * @param array $description
     * @return Module
     */
    public function setDescription($description)
    {
        $n = $this->getDescription();

        foreach($description as $k=>$v)
        {
            $n[$k] = $v;
        }

        $this->description = $n;

        return $this;

    }

    /**
     * Get description
     *
     * @param $locale
     * @return array
     */
    public function getDescription($locale="")
    {
        $n = $this->description;

        if($locale && isset($n[$locale]))
        {
            $n = $n[$locale];
        }

        return $n;

    }

    /**
     * Set contentFilterField
     *
     * @param string $contentFilterField
     * @return Module
     */
    public function setContentFilterField($contentFilterField)
    {
        $this->contentFilterField = $contentFilterField;

        return $this;
    }

    /**
     * Get contentFilterField
     *
     * @return string
     */
    public function getContentFilterField()
    {
        return $this->contentFilterField;
    }

    /**
     * Set contentOrderByField
     *
     * @param string $contentOrderByField
     * @return Module
     */
    public function setContentOrderByField($contentOrderByField)
    {
        $this->contentOrderByField = $contentOrderByField;

        return $this;
    }

    /**
     * Get contentOrderByField
     *
     * @return string
     */
    public function getContentOrderByField()
    {
        return $this->contentOrderByField;
    }

    /**
     * Set singleContentItem
     *
     * @param \SSone\CMSBundle\Entity\Content $singleContentItem
     * @return Module
     */
    public function setSingleContentItem(\SSone\CMSBundle\Entity\Content $singleContentItem = null)
    {
        $this->singleContentItem = $singleContentItem;

        return $this;
    }

    /**
     * Get singleContentItem
     *
     * @return \SSone\CMSBundle\Entity\Content
     */
    public function getSingleContentItem()
    {
        return $this->singleContentItem;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Module
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set form
     *
     * @param \SSone\CMSBundle\Entity\CMSForm $form
     * @return Module
     */
    public function setForm(\SSone\CMSBundle\Entity\CMSForm $form = null)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Get form
     *
     * @return \SSone\CMSBundle\Entity\CMSForm
     */
    public function getForm()
    {
        return $this->form;
    }
}
