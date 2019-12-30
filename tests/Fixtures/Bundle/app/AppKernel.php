<?php

declare(strict_types=1);

use Alpha\TwigBundle\AlphaTwigBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new TwigBundle(),
            new AlphaTwigBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config/config.yml');
    }

    public function getLogDir(): string
    {
        return $this->getVarDir().'/logs';
    }

    public function getCacheDir(): string
    {
        return $this->getVarDir().'/cache/'.$this->getEnvironment();
    }

    private function getVarDir(): string
    {
        if (method_exists($this, 'getProjectDir')) {
            return $this->getProjectDir().'/var';
        }
        if (method_exists($this, 'getRootDir')) {
            return $this->getRootDir().'/../../../../var';
        }

        throw new \RuntimeException('Cannot work out where is the project specific var directory');
    }
}
