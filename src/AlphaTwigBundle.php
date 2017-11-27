<?php

namespace Alpha\TwigBundle;

use Alpha\TwigBundle\DependencyInjection\Compiler\TwigLoaderPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AlphaTwigBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TwigLoaderPass());
    }
}
