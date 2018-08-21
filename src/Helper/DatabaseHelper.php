<?php

declare(strict_types=1);

namespace Alpha\TwigBundle\Helper;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Migration;

class DatabaseHelper
{
    private $connection;
    private $migrationsDir;

    public function __construct(Connection $connection, string $migrationsDir)
    {
        $this->connection = $connection;
        $this->migrationsDir = $migrationsDir;
    }

    public function cleanDatabase(): void
    {
        $this->dropDatabase();
        $this->createDatabase();
        $this->runMigrations();
    }

    private function createDatabase(): void
    {
        try {
            $this->connection->getSchemaManager()->createDatabase($this->connection->getDatabase());
        } catch (\PDOException $e) {
            // ignore exception, the database might not exist already
        }
    }

    private function dropDatabase(): void
    {
        $this->connection->getSchemaManager()->dropDatabase($this->connection->getDatabase());
    }

    /**
     * Runs the command doctrine:migrations:migrate
     */
    private function runMigrations(): void
    {
        try {
            $this->connection->executeQuery(sprintf('USE %s', $this->connection->getDatabase()));

            $config = new Configuration($this->connection);
            $config->setMigrationsTableName('migration_versions');
            $config->setMigrationsNamespace('Application\\Migrations');
            $config->setMigrationsDirectory($this->migrationsDir);
            $config->registerMigrationsFromDirectory($config->getMigrationsDirectory());

            $migration = new Migration($config);
            $migration->migrate();
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Could not run the migrations. Error message: %s', $e->getMessage()));
        }
    }
}
