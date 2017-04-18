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

class LoadCMSBaseData implements FixtureInterface, ContainerAwareInterface
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

        $this->createAdminUser($om);

        $this->createFieldTYpes($om);

        $om->flush();

    }

    private function createAdminUser($om)
    {

        //Create the default admin role
        $adminRole = new Role();
        $adminRole->setRole("ROLE_ADMIN");
        $adminRole->setName("Administrator");

        //Create the default super admin role
        $superAdminRole = new Role();
        $superAdminRole->setRole("ROLE_SUPER_ADMIN");
        $superAdminRole->setName("Super Administrator");

        //Create the default developer admin role
        $maintenanceRole = new Role();
        $maintenanceRole->setRole("ROLE_MAINTENANCE");
        $maintenanceRole->setName("Maintenance User");

        //Create the default admin user
        $user = new User();
        $user->setUsername("admin");
        $user->setEmail("admin@admin.co");
        $user->addRole($adminRole);
        $user->addRole($superAdminRole);
        $user->addRole($maintenanceRole);

        $factory = $this->container->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword('admin', $user->getSalt());
        $user->setPassword($password);

        $om->persist($adminRole);
        $om->persist($superAdminRole);
        $om->persist($maintenanceRole);
        $om->persist($user);

    }

    private function createFieldTypes($om)
    {

        //Create Single Text

    }
}