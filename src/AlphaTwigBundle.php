<?php

declare(strict_types=1);

namespace Alpha\TwigBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AlphaTwigBundle extends Bundle
{
    private const PARAM_TEMPLATE_MAPPING_DIR = 'alpha_twig.entity.template.mapping_dir';
    private const PARAM_TEMPLATE_MAPPING_NAMESPACE = 'alpha_twig.entity.template.class';
    private const DEFAULT_TEMPLATE_MAPPING_NAMESPACE = 'Alpha\TwigBundle\Entity';

    public function build(ContainerBuilder $container): void
    {
        $this->addRegisterMappingsPass($container);
    }

    private function addRegisterMappingsPass(ContainerBuilder $container): void
    {
        $mappings = [
            $this->deriveMappingDir($container) => $this->deriveMappingNamespace($container),
        ];

        $container->addCompilerPass(DoctrineOrmMappingsPass::createYamlMappingDriver($mappings));
    }

    private function deriveMappingDir(ContainerBuilder $container): string
    {
        if ($container->hasParameter(self::PARAM_TEMPLATE_MAPPING_DIR)) {
            return $container->getParameter(self::PARAM_TEMPLATE_MAPPING_DIR);
        }

        return realpath(__DIR__.'/Resources/config/doctrine');
    }

    private function deriveMappingNamespace(ContainerBuilder $container): string
    {
        if ($container->hasParameter(self::PARAM_TEMPLATE_MAPPING_NAMESPACE)) {
            $namespace = preg_replace(
                '#^(.*)\\\\[^\\\\]+$#',
                '$1',
                $container->getParameter(self::PARAM_TEMPLATE_MAPPING_NAMESPACE)
            );

            return $namespace ?: '\\';
        }

        return self::DEFAULT_TEMPLATE_MAPPING_NAMESPACE;
    }
}
