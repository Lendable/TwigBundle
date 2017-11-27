<?php

namespace Alpha\TwigBundle\Extension;

use Symfony\Component\Yaml\Dumper;

class YamlDumpExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            'yaml_dump' => new \Twig_SimpleFilter($this, 'yamlDump')
        ];
    }

    /**
     * Convert array to yaml string
     *
     * @param array $data
     *
     * @param int $level
     * @return string The YAML representation of the array
     */
    public function yamlDump(array $data, int $level = 0): string
    {
        $dumper = new Dumper();

        return $dumper->dump($data, $level);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName(): string
    {
        return 'yaml_dump_extension';
    }
}
