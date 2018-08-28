<?php

declare(strict_types=1);

namespace Alpha\TwigBundle\DependencyInjection\Compiler;

use Alpha\TwigBundle\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Exception\OutOfBoundsException;

class TwigLoaderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->validateServices($container);

        $this->appendNewLoaderToTheExistingChain($container);

        $this->updateTwig($container);
    }

    private function validateServices(ContainerBuilder $container): void
    {
        foreach ([
            'twig.loader.chain',
            'twig',
            'twig.loader.filesystem',
            'alpha_twig.loader.database',
        ] as $service) {
            if (false === $container->hasDefinition($service)) {
                throw new ServiceNotFoundException(sprintf('The service %s does not exist.', $service));
            }
        }
    }

    private function appendNewLoaderToTheExistingChain(ContainerBuilder $container): void
    {
        $chainLoaderDefinition = $container->getDefinition('twig.loader.chain');

        try {
            $existingChain = $chainLoaderDefinition->getArgument(0);
            if (in_array('alpha_twig.loader.database', $existingChain)) {
                return;
            }
            $existingChain[] = $container->getDefinition('alpha_twig.loader.database');
            $chainLoaderDefinition->replaceArgument(0, $existingChain);
        } catch (OutOfBoundsException $exception) {
            $newChain = [
                [
                    $container->getDefinition('twig.loader.filesystem'),
                    $container->getDefinition('alpha_twig.loader.database'),
                ],
            ];
            $chainLoaderDefinition->setArguments($newChain);
        }
    }

    private function updateTwig(ContainerBuilder $container): void
    {
        $chainLoaderDefinition = $container->getDefinition('twig.loader.chain');
        $twigDefinition = $container->getDefinition('twig');
        $twigDefinition->replaceArgument(0, $chainLoaderDefinition);
    }
}
