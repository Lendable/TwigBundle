<?php

declare(strict_types=1);

namespace Alpha\TwigBundle\Tests\Functional;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Alpha\TwigBundle\Entity\Template;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;

class TwigControllerTest extends TestCase
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    protected function setUp(): void
    {
        $this->kernel = new \AppKernel('test', false);
        $this->kernel->boot();

        $entityManager = $this->kernel->getContainer()->get('doctrine.orm.entity_manager');
        assert($entityManager instanceof EntityManagerInterface);
        $this->entityManager = $entityManager;

        $twig = $this->kernel->getContainer()->get('twig');
        assert($twig instanceof \Twig_Environment);
        $this->twig = $twig;

        $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($this->kernel);
        $application->setAutoExit(false);

        $application->add(new \Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand());
        $application->run(new \Symfony\Component\Console\Input\ArrayInput([
            'command' => 'doctrine:database:drop',
            '--force' => true,
        ]), new NullOutput());

        $application->add(new \Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand());
        $application->run(new \Symfony\Component\Console\Input\ArrayInput([
            'command' => 'doctrine:database:create',
        ]), new NullOutput());

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
        ]), new NullOutput());
    }

    protected function tearDown(): void
    {
        $this->kernel->shutdown();
    }

    /**
     * @test
     * @group database
     */
    public function compiling_a_database_template_succeeds_if_it_exists(): void
    {
        $template = new Template();
        $template->setName('hello.txt.twig');
        $template->setSource('Hello {{ name }}.');
        $template->setLastModified(new \DateTimeImmutable());

        $this->entityManager->persist($template);
        $this->entityManager->flush();

        $this->assertSame('Hello Database.', $this->twig->render('hello.txt.twig', ['name' => 'Database']));
    }

    /**
     * @test
     * @group database
     */
    public function compiling_a_database_template_throws_exception_if_it_does_not_exist(): void
    {
        $this->expectException(\Twig_Error_Loader::class);

        $this->twig->render('invalid.txt.twig', ['name' => 'World']);
    }

    /**
     * @test
     * @group database
     */
    public function compiling_a_file_succeeds(): void
    {
        $output = $this->twig->render('AlphaTwigBundle:Test:hello.txt.twig', ['name' => 'File']);

        $this->assertSame('Hello File.', $output);
    }

    /**
     * @test
     * @group database
     */
    public function file_takes_precedence_over_database(): void
    {
        $template = new Template();
        $template->setName('AlphaTwigBundle:Test:hello.txt.twig');
        $template->setSource('Database says hi to {{ name }}.');
        $template->setLastModified(new \DateTimeImmutable());

        $this->entityManager->persist($template);
        $this->entityManager->flush();

        $output = $this->twig->render('AlphaTwigBundle:Test:hello.txt.twig', ['name' => 'File']);

        $this->assertSame('Hello File.', $output);
    }
}
