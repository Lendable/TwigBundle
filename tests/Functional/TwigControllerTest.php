<?php

namespace Alpha\TwigBundle\Tests\Functional;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Alpha\TwigBundle\Entity\Template;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Alpha\TwigBundle\Helper\DatabaseHelper;
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
    private $em;

    protected function setUp()
    {
        $this->kernel = new \AppKernel('test', true);
        $this->kernel->boot();
        
        $this->em = $this->kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->twig = $this->kernel->getContainer()->get('twig');
    }

    protected function tearDown()
    {
        $this->kernel->shutdown();
    }

    /**
     * @test
     * @group database
     */
    public function compiling_a_database_template_succeeds_if_it_exists()
    {
        $template = new Template();
        $template->setName('hello.txt.twig');
        $template->setSource('Hello {{ name }}.');
        $template->setLastModified(new \DateTime());

        $this->em->persist($template);
        $this->em->flush();

        $this->assertSame('Hello Database.', $this->twig->render('hello.txt.twig', ['name' => 'Database']));
    }

    /**
     * @test
     * @group database
     */
    public function compiling_a_database_template_throws_exception_if_it_does_not_exist()
    {
        $this->expectException(\Twig_Error_Loader::class);

        $this->twig->render('invalid.txt.twig', ['name' => 'World']);
    }

    /**
     * @test
     * @group database
     */
    public function compiling_a_file_succeeds()
    {
        $output = $this->twig->render('AlphaTwigBundle:Test:hello.txt.twig', ['name' => 'File']);

        $this->assertSame('Hello File.', $output);
    }

    /**
     * @test
     * @group database
     */
    public function file_takes_precedence_over_database()
    {
        $template = new Template();
        $template->setName('AlphaTwigBundle:Test:hello.txt.twig');
        $template->setSource('Database says hi to {{ name }}.');
        $template->setLastModified(new \DateTime());

        $this->em->persist($template);
        $this->em->flush();

        $output = $this->twig->render('AlphaTwigBundle:Test:hello.txt.twig', ['name' => 'File']);

        $this->assertSame('Hello File.', $output);
    }
}
