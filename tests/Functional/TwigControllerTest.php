<?php

declare(strict_types=1);

namespace Alpha\TwigBundle\Tests\Functional;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Alpha\TwigBundle\Entity\Template;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;
use Twig\Error\LoaderError;

class TwigControllerTest extends TestCase
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var Environment
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

        $container = $this->kernel->getContainer();
        assert($container instanceof ContainerInterface);

        $entityManager = $container->get('doctrine.orm.entity_manager');
        assert($entityManager instanceof EntityManagerInterface);
        $this->entityManager = $entityManager;

        $twig = $container->get('twig');
        assert($twig instanceof Environment);
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
        $this->expectException(LoaderError::class);

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
