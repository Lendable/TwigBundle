<?php

namespace Alpha\TwigBundle\Extension;

class UrlDecodeExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return array(
            'url_decode' => new \Twig_SimpleFilter($this, 'urlDecode')
        );
    }

    /**
     * URL Decode a string
     *
     * @param string $url
     *
     * @return string The decoded URL
     */
    public function urlDecode(string $url): string
    {
        return urldecode($url);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName(): string
    {
        return 'url_decode_extension';
    }
}
