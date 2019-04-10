<?php

declare(strict_types=1);

namespace Alpha\TwigBundle\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class UrlDecodeExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('url_decode', [$this, 'urlDecode']),
        ];
    }

    public function urlDecode(string $url): string
    {
        return urldecode($url);
    }
}
