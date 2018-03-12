<?php

namespace Alpha\TwigBundle\Tests\Functional;

use Alpha\TwigBundle\AlphaTwigBundle;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Alpha\TwigBundle\Entity\Template;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Alpha\TwigBundle\Helper\DatabaseHelper;

class TwigControllerTest extends WebTestCase
{
    private $em;

    public function setUp()
    {
        self::createClient([
            'environment' => 'test',
            'debug' => true,
        ]);

        $this->em = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');

        $loader = require self::$kernel->getContainer()->getParameter('kernel.root_dir').'/../vendor/autoload.php';

        AnnotationRegistry::registerLoader([$loader, 'loadClass']);

        $database = new DatabaseHelper(
            self::$kernel->getContainer()->get('database_connection'),
            self::$kernel->getContainer()->get('doctrine.orm.entity_manager'),
            self::$kernel->getContainer()->getParameter('kernel.root_dir').'/DoctrineMigrations'
        );
        $database->cleanDatabase();
    }

    /**
     * @group database
     */
    public function testCompilingADatatabaseTemplateSucceedsIfItExists()
    {
        $template = new Template();
        $template->setName('hello.txt.twig');
        $template->setSource('Hello {{ name }}.');
        $template->setLastModified(new \DateTime());

        $this->em->persist($template);
        $this->em->flush();

        /** @var \Twig_Environment $twig */
        $twig = self::$kernel->getContainer()->get('twig');
        $this->assertSame('Hello Database.', $twig->render('hello.txt.twig', ['name' => 'Database']));
    }

    /**
     * @expectedException Twig_Error_Loader
     * @group database
     */
    public function testCompilingADatatabaseTemplateThrowsExceptionIfDoesNotExist()
    {
        /** @var \Twig_Environment $twig */
        $twig = self::$kernel->getContainer()->get('twig');
        $twig->render('invalid.txt.twig', ['name' => 'World']);
    }

    /**
     * @group database
     */
    public function testCompilingAFileSucceeds()
    {
        /** @var \Twig_Environment $twig */
        $twig = self::$kernel->getContainer()->get('twig');
        $output = $twig->render('AlphaTwigBundle:Test:hello.txt.twig', ['name' => 'File']);

        $this->assertSame('Hello File.', $output);
    }

    /**
     * @group database
     */
    public function testFileTakesPrecedenceOverDatabase()
    {
        $template = new Template();
        $template->setName('AlphaTwigBundle:Test:hello.txt.twig');
        $template->setSource('Database says hi to {{ name }}.');
        $template->setLastModified(new \DateTime());

        $this->em->persist($template);
        $this->em->flush();

        /** @var \Twig_Environment $twig */
        $twig = self::$kernel->getContainer()->get('twig');
        $output = $twig->render('AlphaTwigBundle:Test:hello.txt.twig', ['name' => 'File']);

        $this->assertSame('Hello File.', $output);
    }
}
