<?php

//php app/console doctrine:fixtures:load --append

namespace JfxNinja\CMSBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use JfxNinja\CMSBundle\Entity\User;
use JfxNinja\CMSBundle\Entity\Role;
use JfxNinja\CMSBundle\Entity\FieldType;
use JfxNinja\CMSBundle\Entity\FieldSetupOptions;

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