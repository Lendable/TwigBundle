[![Build Status](https://api.travis-ci.org/Lendable/TwigBundle.svg)](https://travis-ci.org/Lendable/TwigBundle)

TwigBundle
==========

Symfony bundle that allows Twig templates to be loaded from database store.

Install
=======

`composer require lendable/twig-bundle ~1.0`


Require the bundle in your AppKernel.php

    <?php
    
    class AppKernel extends \Symfony\Component\HttpKernel\Kernel
    {
        public function registerBundles(): array
        {
            $bundles = [
                // ...
                new Alpha\TwigBundle\AlphaTwigBundle(),
            ];
        }
        
        // ...
    }
    
    
You can use the provided [`Template`](https://github.com/Lendable/TwigBundle/blob/master/src/Entity/Template.php) entity or use your own. Overwrite the bundle entity with setting a value for paramater `alpha_twig.entity.template.class`.

    // config.yml
    parameters:
        alpha_twig.entity.template.class: 'Alpha\TwigBundle\Entity\Template'

License
=======
[MIT](https://opensource.org/licenses/MIT)
