<?php

namespace JfxNinja\CMSBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

use Doctrine\DBAL\Migrations\Migration;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\ORM\EntityManager;

use JfxNinja\CMSBundle\JfxNinjaCMSBundle;

/**
 * A simple service to audit record creation and modification with user and timestamp
 */
class MigrationService
{

    private $container;
    private $entityManager;
    private $rootDir;

    public function __construct(ContainerInterface $container, EntityManager $entityManager, $rootDir)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
        $this->rootDir = $rootDir;
    }


    public function migrate()
    {

        $container = $this->container;
        $conn = $this->entityManager->getConnection();

        $jfxninjaCMSbundle = new JfxNinjaCMSBundle();
        $dir = $jfxninjaCMSbundle->getPath() . "/Migrations";

        $configuration = new Configuration($conn);
        $configuration->setMigrationsNamespace('JfxNinja\CMSBundle\Migrations');
        $configuration->setMigrationsDirectory($dir);
        $configuration->registerMigrationsFromDirectory($dir);
        $configuration->setName('One CMS Migrations');
        $configuration->setMigrationsTableName('cms_migrations');
        $versions = $configuration->getMigrations();
        foreach ($versions as $version) {
            $migration = $version->getMigration();
            if ($migration instanceof ContainerAwareInterface) {
                $migration->setContainer($container);
            }
        }
        $migration = new Migration($configuration);
        $migrated = $migration->migrate();
    }


}