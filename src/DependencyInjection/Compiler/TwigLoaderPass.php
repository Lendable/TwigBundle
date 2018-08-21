<?php

declare(strict_types=1);

namespace Alpha\TwigBundle\DependencyInjection\Compiler;

use Alpha\TwigBundle\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class TwigLoaderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->validateServices(
            $container,
            [
                'twig.loader.chain',
                'twig',
                'twig.loader.filesystem',
                'alpha_twig.loader.database',
            ]
        );

        $twigChainLoaderDefinition = $container->getDefinition('twig.loader.chain');
        $arguments = [
            [
                $container->getDefinition('twig.loader.filesystem'),
                $container->getDefinition('alpha_twig.loader.database'),
            ]
        ];
        $twigChainLoaderDefinition->setArguments($arguments);

        $twigDefinition = $container->getDefinition('twig');
        $twigDefinition->replaceArgument(0, $twigChainLoaderDefinition);
    }

    private function validateServices(ContainerBuilder $container, array $services): void
    {
        foreach ($services as $service) {
            if (false === $container->hasDefinition($service)) {
                throw new ServiceNotFoundException(sprintf('The service %s does not exist.', $service));
            }
        }
    }
}
