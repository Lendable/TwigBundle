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
            $this->resolveMappingDir($container) => $this->resolveMappingNamespace($container),
        ];

        $container->addCompilerPass(DoctrineOrmMappingsPass::createYamlMappingDriver($mappings));
    }

    private function resolveMappingDir(ContainerBuilder $container): string
    {
        if ($container->hasParameter(self::PARAM_TEMPLATE_MAPPING_DIR)) {
            return $container->getParameter(self::PARAM_TEMPLATE_MAPPING_DIR);
        }

        $realpath = realpath(__DIR__.'/Resources/config/doctrine');
        if ($realpath === false) {
            throw new \RuntimeException('Cannot resolve the location of the mapping directory');
        }

        return $realpath;
    }

    private function resolveMappingNamespace(ContainerBuilder $container): string
    {
        if ($container->hasParameter(self::PARAM_TEMPLATE_MAPPING_NAMESPACE)) {
            $namespace = preg_replace(
                '#^(.*)\\\\[^\\\\]+$#',
                '$1',
                $container->getParameter(self::PARAM_TEMPLATE_MAPPING_NAMESPACE)
            );

            return is_string($namespace) && !empty($namespace) ? $namespace : '\\';
        }

        return self::DEFAULT_TEMPLATE_MAPPING_NAMESPACE;
    }
}
