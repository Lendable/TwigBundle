<?php

declare(strict_types=1);

namespace Alpha\TwigBundle;

use Alpha\TwigBundle\DependencyInjection\Compiler\TwigLoaderPass;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AlphaTwigBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new TwigLoaderPass());

        $this->addRegisterMappingsPass($container);
    }

    private function addRegisterMappingsPass(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('alpha_twig.entity.template.mapping_dir')) {
            $mappingDir = realpath(__DIR__.'/Resources/config/doctrine');
        } else {
            $mappingDir = $container->getParameter('alpha_twig.entity.template.mapping_dir');
        }

        if (!$container->hasParameter('alpha_twig.entity.template.class')) {
            $mappingNamespace = 'Alpha\TwigBundle\Entity';
        } else {
            $namespaceParts = explode('\\', $container->getParameter('alpha_twig.entity.template.class'));
            if (count($namespaceParts) > 1) {
                array_pop($namespaceParts);
            }
            $mappingNamespace = count($namespaceParts) > 0 ? implode('\\', $namespaceParts) : '\\';
        }

        $mappings = [
            $mappingDir => $mappingNamespace,
        ];
        $container->addCompilerPass(DoctrineOrmMappingsPass::createYamlMappingDriver($mappings));
    }
}
