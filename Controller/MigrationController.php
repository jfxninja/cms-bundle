<?php
namespace Acme\DemiBundle\Controller;
use Doctrine\DBAL\Migrations\Migration;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
class MigrateController extends BaseController
{
    public function indexAction()
    {
        $container = $this->container;
        $conn = $this->get('doctrine')->getConnection();
        $dir = $container->getParameter('doctrine_migrations.dir_name');
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $configuration = new Configuration($conn);
        $configuration->setMigrationsNamespace($container->getParameter('doctrine_migrations.namespace'));
        $configuration->setMigrationsDirectory($dir);
        $configuration->registerMigrationsFromDirectory($dir);
        $configuration->setName($container->getParameter('doctrine_migrations.name'));
        $configuration->setMigrationsTableName($container->getParameter('doctrine_migrations.table_name'));
        $versions = $configuration->getMigrations();
        foreach ($versions as $version) {
            $migration = $version->getMigration();
            if ($migration instanceof ContainerAwareInterface) {
                $migration->setContainer($container);
            }
        }
        $migration = new Migration($configuration);
        $migrated = $migration->migrate();

        // ...
    }
}