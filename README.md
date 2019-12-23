[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Lendable/TwigBundle/badges/quality-score.png)](https://scrutinizer-ci.com/g/Lendable/TwigBundle/?branch=master)
[![Build Status](https://api.travis-ci.org/Lendable/TwigBundle.svg?branch=master)](https://www.travis-ci.org/Lendable/TwigBundle)
[![Coverage Status](https://coveralls.io/repos/github/Lendable/TwigBundle/badge.svg?branch=master)](https://coveralls.io/github/Lendable/TwigBundle?branch=master)
[![Latest Stable Version](https://poser.pugx.org/lendable/twig-bundle/version)](https://packagist.org/packages/lendable/twig-bundle)
[![Total Downloads](https://poser.pugx.org/lendable/twig-bundle/downloads)](https://packagist.org/packages/lendable/twig-bundle)

TwigBundle
==========

Symfony bundle that allows Twig templates to be loaded from database store.

Install
===

`composer require lendable/twig-bundle`


Require the bundle in your AppKernel.php

```php
<?php

class YourAppKernel extends \Symfony\Component\HttpKernel\Kernel
{
    public function registerBundles(): array
    {
        $bundles = [
            // ...
            new Alpha\TwigBundle\AlphaTwigBundle(),
        ];
        
        return $bundles;
    }
    
    // ...
}
```
    
You can use the provided [`Template`](https://github.com/Lendable/TwigBundle/blob/master/src/Entity/Template.php) entity or use your own. Overwrite the bundle entity with setting paramater values for your entity class  `alpha_twig.entity.template.class` and the directory containing its YAML mapping `alpha_twig.entity.template.mapping_dir`.

```yml
// your-application/app/config/config.yml
parameters:
    alpha_twig.entity.template.class: 'Alpha\TwigBundle\Entity\Template'
    alpha_twig.entity.template.mapping_dir: 'src/Resources/config/doctrine'
```

License
===
[MIT](https://opensource.org/licenses/MIT)
