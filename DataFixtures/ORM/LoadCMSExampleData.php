<?php

//php app/console doctrine:fixtures:load --append

namespace SSone\CMSBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use SSone\CMSBundle\Entity\User;
use SSone\CMSBundle\Entity\Role;
use SSone\CMSBundle\Entity\FieldType;
use SSone\CMSBundle\Entity\FieldSetupOptions;

class LoadCMSExampleeData implements FixtureInterface, ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $om)
    {

        //Create default contentType
        //Create default contentType attr and var fields


        //Create default content

        //Create default menu

        //Create default menu item

        $om->flush();

    }

}