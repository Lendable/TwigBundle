<?php

declare(strict_types=1);

namespace Alpha\TwigBundle\Extension;

class UrlDecodeExtension extends \Twig_Extension
{
    public function getFilters(): array
    {
        return [
            new \Twig_SimpleFilter('url_decode', [$this, 'urlDecode']),
        ];
    }

    public function urlDecode(string $url): string
    {
        return urldecode($url);
    }
}
