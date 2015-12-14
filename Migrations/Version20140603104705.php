<?php

namespace SSone\CMSBundle\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use SSone\CMSBundle\Entity\FieldType;
use SSone\CMSBundle\Entity\FieldSetupOptions;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140603104705 extends AbstractMigration implements ContainerAwareInterface
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


        $wysiwyg = new Fieldtype();
        $wysiwyg
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setVariableName("wysiwyg")
            ->setName("WYSIWYG");
        $wysiwygUploadPath= new FieldSetupOptions();
        $wysiwygUploadPath
        ->setCreatedBy("migration")
        ->setModifiedBy("migration")
        ->setName("WYSIWYG upload path")
        ->setLabel("Image upload path (assets/[path])")
        ->setInputType("text")
        ->setVariableName("wysiwygUploadPath")
        ->setFieldType($wysiwyg);
        $wysiwygOptions= new FieldSetupOptions();
        $wysiwygOptions
            ->setCreatedBy("migration")
            ->setModifiedBy("migration")
            ->setName("WYSIWYG setup options")
            ->setLabel("WYSIWYG setup options (js)")
            ->setInputType("textarea")
            ->setVariableName("wysiwygsetupoptions")
            ->setFieldType($wysiwyg);

        $em->persist($wysiwyg);
        $em->persist($wysiwygOptions);
        $em->persist($wysiwygUploadPath);
        $em->flush();
    }


}
