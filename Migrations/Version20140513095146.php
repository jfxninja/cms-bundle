<?php

namespace JfxNinja\CMSBundle\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use JfxNinja\CMSBundle\Entity\Language;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140513095146 extends AbstractMigration implements ContainerAwareInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }

    public function postUp(Schema $schema)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');

        $defaultLanguage = new Language();
        $defaultLanguage
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setName("English")
            ->setisDefault(1)
            ->setLanguageCode("en");
        $em->persist($defaultLanguage);
        $em->flush();
    }
}
