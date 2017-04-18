<?php

namespace JfxNinja\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Util\SecureRandom;

/**
 * @ORM\Entity(repositoryClass="JfxNinja\CMSBundle\Entity\BlockFieldsRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="ninjacms_blockFields")
 */
class BlockField {

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
     * @ORM\Column(name="sort", type="integer", nullable=true)
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
     * @ORM\Column(name="fieldContent", type="array", length=3000, nullable=true)
     */
    private $fieldContent;

    /**
     * @ORM\ManyToOne(targetEntity="Block", inversedBy="blockFields" )
     * @ORM\JoinColumn(name="fk_block_id", referencedColumnName="id")
     */
    protected $block;


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
     * @return BlockField
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
     * @return BlockField
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
     * @return BlockField
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
     * @return BlockField
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
     * @return BlockField
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
     * Set block
     *
     * @param \jfxninja\CMSBundle\Entity\Block $block
     * @return BlockField
     */
    public function setBlock(\jfxninja\CMSBundle\Entity\Block $block = null)
    {

        $this->block = $block;

        return $this;
    }

    /**
     * Get block
     *
     * @return \jfxninja\CMSBundle\Entity\Block
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * Set fieldContent
     *
     * @param array $fieldContent
     * @return BlockField
     */
    public function setFieldContent($fieldContent)
    {

        //merge multi language content

        $c = $this->getFieldContent();

        //loop through field inputs
        foreach($fieldContent as $k=>$input)
        {
            //check for multilanguage data and update language key
            if(is_array($input))
            {
                $isMultiLanguage = true;

                foreach(array_keys($input) as $arrayKey)
                {
                    if(strlen($arrayKey) > 2)
                    {
                        $isMultiLanguage = false;
                    }
                }

                if($isMultiLanguage)
                {
                    //If this is the first time storing a translation we need to set the
                    //data point
                    if(!isset($c[$k])) $c[$k] = array();

                    //If the currently stored value is not an array this means that the
                    //field was not previously translatable so first we need to initialise the
                    //variable point as an array.
                    if(!is_array($c[$k])) $c[$k] = array();

                    //Merge the translation with the current field value
                    $c[$k][key($input)] = current($input);
                }
                else //The data is an array but not multilangage
                {
                    $c[$k] = $input;
                }

            }
            else
            {
                //THis is a non translatable field so just store the value
                $c[$k] = $input;
            }
        }

        $this->fieldContent = $c;

        return $this;
    }

    /**
     * Get fieldContent
     *
     * @return array 
     */
    public function getFieldContent()
    {
        return $this->fieldContent;
    }

    /**
     * Set sort
     *
     * @param integer $sort
     * @return BlockField
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
}
