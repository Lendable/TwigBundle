<?php

namespace Alpha\TwigBundle\Extension;

class UrlDecodeExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new \Twig_SimpleFilter('url_decode', [$this, 'urlDecode']),
        ];
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
}
