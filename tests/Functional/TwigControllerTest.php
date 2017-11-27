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
        $container = new ContainerBuilder(
            new ParameterBag(
                [
                    'kernel.debug' => true,
                    'kernel.name' => 'tests',
                    'kernel.bundles' => [
                        'AlphaTwigBundle' => AlphaTwigBundle::class,
                    ],
                    'kernel.cache_dir' => sys_get_temp_dir().'/lendable-twig-bundle-test',
                    'kernel.environment' => 'test',
                    'kernel.root_dir' => __DIR__.'/../src',
                ]
            )
        );

        $doctrineExtension = new DoctrineExtension();
        $container->registerExtension($doctrineExtension);

        $doctrineExtension->load(
            [
                [
                    'dbal' => [
                        'connections' => [
                            'default' => [
                                'driver' => 'pdo_sqlite',
                                'charset' => 'UTF8',
                                'memory' => true,
                            ],
                        ],
                    ],
                    'orm' => [
                        'default_entity_manager' => 'default',
                        'entity_managers' => [
                            'default' => [
                                'mappings' => [
                                    'FooBundle' => [
                                        'type' => 'yml',
                                        'dir' => __DIR__.'/../../src/Entity/',
                                        'prefix' => 'Alpha\TwigBundle\Entity',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            $container
        );

        $doctrineExtensionsExtension = new LendableDoctrineExtensionsExtension();
        $container->registerExtension($doctrineExtensionsExtension);

        $doctrineExtensionsExtension->load(
            [
                [
                    'repositories' => [
                        CustomRepositoryWithCustomArgs::class => [
                            'entity' => WithCustomRepository::class,
                            'managers' => ['default'],
                            'args' => [
                                'foo',
                                '%custom_parameter%',
                                '@custom_service',
                                [
                                    'key1' => 'bar',
                                    'key2' => '%custom_parameter%',
                                    'key3' => '@custom_service',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            $container
        );

        $container->getCompilerPassConfig()->setBeforeOptimizationPasses([new RepositoryServicesCompilerPass()]);

        $container->setParameter('custom_parameter', 'custom_parameter_value');
        $container->setDefinition('custom_service', new Definition(CustomService::class));

        $container->compile();

        return $container;

        $this->em = $container->get('doctrine.orm.entity_manager');

        $loader = require self::$kernel->getContainer()->getParameter('kernel.root_dir') . '/../vendor/autoload.php';

        AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

        $database = new DatabaseHelper(
            self::$kernel->getContainer()->get('database_connection'),
            self::$kernel->getContainer()->get('doctrine.orm.entity_manager'),
            self::$kernel->getContainer()->getParameter('kernel.root_dir') . '/DoctrineMigrations'
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

    /**
     * @group database
     */
    public function testStringGetsParsed()
    {
        /** @var \Twig_Environment $twig */
        $twig = self::$kernel->getContainer()->get('twig');
        $output = $twig->render('{{ name }} says hi!', ['name' => 'String']);

        $this->assertSame('String says hi!', $output);
    }
}
