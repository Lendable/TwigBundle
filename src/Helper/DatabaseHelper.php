<?php

namespace Alpha\TwigBundle\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Migration;
use PDOException;
use RuntimeException;
use Exception;

class DatabaseHelper
{
    private $connection;
    private $entityManager;
    private $migrationsDir;

    public function __construct(Connection $connection, EntityManager $entityManager, $migrationsDir)
    {
        $this->connection = $connection;
        $this->entityManager = $entityManager;
        $this->migrationsDir = $migrationsDir;
    }

    public function cleanDatabase()
    {
        $this->dropDatabase();
        $this->createDatabase();
        $this->runMigrations();
    }

    private function createDatabase()
    {
        try {
            $this->connection->getSchemaManager()->createDatabase($this->connection->getDatabase());
        } catch (PDOException $e) {
            // ignore exception, the database might not exist already
        }
    }

    private function dropDatabase()
    {
        $this->connection->getSchemaManager()->dropDatabase($this->connection->getDatabase());
    }

    /**
     * Runs the command doctrine:migrations:migrate
     */
    private function runMigrations()
    {
        $this->connection->executeQuery(sprintf('USE %s', $this->connection->getDatabase()));

        $config = new Configuration($this->connection);
        $config->setMigrationsTableName('migration_versions');
        $config->setMigrationsNamespace('Application\\Migrations');
        $config->setMigrationsDirectory($this->migrationsDir);
        $config->registerMigrationsFromDirectory($config->getMigrationsDirectory());

        $migration = new Migration($config);
        try {
            $migration->migrate();
        } catch (Exception $e) {
            throw new RuntimeException(sprintf('Could not run the migrations. Error message: %s', $e->getMessage()));
        }
    }
}
