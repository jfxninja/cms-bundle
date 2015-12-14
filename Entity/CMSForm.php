<?php

namespace SSone\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Util\SecureRandom;

/**
 * @ORM\Entity(repositoryClass="SSone\CMSBundle\Entity\CMSFormRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="ssone_cmsforms")
 */
class CMSForm {

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
     * @ORM\Column(name="formTitle", type="array", nullable=true)
     */
    private $formTitle;

    /**
     * @ORM\Column(name="buttonText", type="string", length=120, nullable=true)
     */
    private $buttonText;


    /**
     * @ORM\Column(name="template", type="string", length=120, nullable=true)
     */
    private $template;

    /**
     * @ORM\Column(name="successURL", type="array", length=120)
     */
    private $successURL;

    /**
     * @ORM\Column(name="sendAdminEmailOnSubmit", type="boolean", nullable=true)
     */
    private $sendAdminEmailOnSubmit;

    /**
     * @ORM\Column(name="adminEmailToAddress", type="text", nullable=true)
     */
    private $adminEmailToAddress;

    /**
     * @ORM\Column(name="adminEmailFromAddress", type="text", nullable=true)
     */
    private $adminEmailFromAddress;

    /**
     * @ORM\Column(name="adminEmailText", type="text", length=120, nullable=true)
     */
    private $adminEmailText;

    /**
     * @ORM\Column(name="adminEmailHTML", type="text", length=120, nullable=true)
     */
    private $adminEmailHTML;



    /**
     * @ORM\ManyToOne(targetEntity="ContentType", inversedBy="blocks")
     * @ORM\JoinColumn(name="fk_contentType_id", referencedColumnName="id")
     */
    private $contentType;


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
     * Set formTitle
     *
     * @param array $formTitle
     * @return Form
     */
    public function setFormTitle($formTitle)
    {
        $s = $this->getFormTitle();
        foreach($formTitle as $k=>$v)
        {
            $s[$k] = $v;
        }
        $this->formTitle = $s;

        return $this;
    }

    /**
     * Get formTitle
     *
     * @param $locale
     * @return array 
     */
    public function getFormTitle($locale = "")
    {
        $n = $this->formTitle;

        if($locale && isset($n[$locale]))
        {
            $n = $n[$locale];
        }

        return $n;
    }

    /**
     * Set contentType
     *
     * @param \SSone\CMSBundle\Entity\ContentType $contentType
     * @return Form
     */
    public function setContentType(\SSone\CMSBundle\Entity\ContentType $contentType = null)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * Get contentType
     *
     * @return \SSone\CMSBundle\Entity\ContentType
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Set sendAdminEmailOnSubmit
     *
     * @param boolean $sendAdminEmailOnSubmit
     * @return CMSForm
     */
    public function setSendAdminEmailOnSubmit($sendAdminEmailOnSubmit)
    {
        $this->sendAdminEmailOnSubmit = $sendAdminEmailOnSubmit;

        return $this;
    }

    /**
     * Get sendAdminEmailOnSubmit
     *
     * @return boolean 
     */
    public function getSendAdminEmailOnSubmit()
    {
        return $this->sendAdminEmailOnSubmit;
    }


    /**
     * Set template
     *
     * @param string $template
     * @return CMSForm
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return string 
     */
    public function getTemplate()
    {
        return $this->template;
    }


    /**
     * Set successURL
     *
     * @param array $successURL
     * @return CMSForm
     */
    public function setSuccessURL($successURL)
    {
        $s = $this->getSuccessURL();
        foreach($successURL as $k=>$v)
        {
            $s[$k] = $v;
        }
        $this->successURL = $s;

        return $this;
    }

    /**
     * Get successURL
     *
     * @param $locale
     * @return array 
     */
    public function getSuccessURL($locale = "")
    {
        $n = $this->successURL;

        if($locale && isset($n[$locale]))
        {
            $n = $n[$locale];
        }

        return $n;
    }


    /**
     * Set adminEmailText
     *
     * @param string $adminEmailText
     * @return CMSForm
     */
    public function setAdminEmailText($adminEmailText)
    {
        $this->adminEmailText = $adminEmailText;

        return $this;
    }

    /**
     * Get adminEmailText
     *
     * @return string 
     */
    public function getAdminEmailText()
    {
        return $this->adminEmailText;
    }

    /**
     * Set adminEmailHTML
     *
     * @param string $adminEmailHTML
     * @return CMSForm
     */
    public function setAdminEmailHTML($adminEmailHTML)
    {
        $this->adminEmailHTML = $adminEmailHTML;

        return $this;
    }

    /**
     * Get adminEmailHTML
     *
     * @return string 
     */
    public function getAdminEmailHTML()
    {
        return $this->adminEmailHTML;
    }

    /**
     * Set adminEmailToAddress
     *
     * @param string $adminEmailToAddress
     * @return CMSForm
     */
    public function setAdminEmailToAddress($adminEmailToAddress)
    {
        $this->adminEmailToAddress = $adminEmailToAddress;

        return $this;
    }

    /**
     * Get adminEmailToAddress
     *
     * @return string 
     */
    public function getAdminEmailToAddress()
    {
        return $this->adminEmailToAddress;
    }

    /**
     * Set adminEmailFromAddress
     *
     * @param string $adminEmailFromAddress
     * @return CMSForm
     */
    public function setAdminEmailFromAddress($adminEmailFromAddress)
    {
        $this->adminEmailFromAddress = $adminEmailFromAddress;

        return $this;
    }

    /**
     * Get adminEmailFromAddress
     *
     * @return string 
     */
    public function getAdminEmailFromAddress()
    {
        return $this->adminEmailFromAddress;
    }

    /**
     * Set buttonText
     *
     * @param string $buttonText
     * @return CMSForm
     */
    public function setButtonText($buttonText)
    {
        $this->buttonText = $buttonText;

        return $this;
    }

    /**
     * Get buttonText
     *
     * @return string 
     */
    public function getButtonText()
    {
        return $this->buttonText;
    }
}
