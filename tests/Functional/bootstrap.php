<?php

declare(strict_types=1);

require_once __DIR__.'/../../vendor/autoload.php';

$kernel = new AppKernel('test', true);
$kernel->boot();

$application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
$application->setAutoExit(false);
$application->add(new \Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand());
$application->add(new \Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand());
$application->add(new \Doctrine\Bundle\DoctrineBundle\Command\Proxy\RunSqlDoctrineCommand());
$application->run(new \Symfony\Component\Console\Input\ArrayInput([
    'command' => 'doctrine:query:sql',
    'sql' => <<<SQL
        CREATE TABLE `template` (
          `id` INTEGER PRIMARY KEY AUTOINCREMENT,
          `name` varchar(255) NOT NULL,
          `source` longtext NOT NULL,
          `services` longtext,
          `lastModified` datetime NOT NULL
        )
SQL
]));

$kernel->shutdown();