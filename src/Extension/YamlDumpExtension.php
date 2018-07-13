<?php

declare(strict_types=1);

namespace Alpha\TwigBundle\Extension;

use Symfony\Component\Yaml\Dumper;

class YamlDumpExtension extends \Twig_Extension
{
    public function getFilters(): array
    {
        return [
            new \Twig_SimpleFilter('yaml_dump', [$this, 'yamlDump']),
        ];
    }

    /**
     * Convert array to yaml string
     *
     * @return string The YAML representation of the array
     */
    public function yamlDump(array $data, int $level = 0): string
    {
        $dumper = new Dumper();

        return $dumper->dump($data, $level);
    }
}
