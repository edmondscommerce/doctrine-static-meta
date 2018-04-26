# Symfony

This document details how to get DSM working with Symfony 4

NOTE: As of writing this you can only install Symfony 4 using composer 1.2.4 or less.

## Setup

* Install symfony 4 [docs](https://symfony.com/doc/current/setup.html)
* Install doctrine-static-meta [docs](../../docs/Getting-Started.md)
    * Gotchas:
        * [`composer.json`](./composer.json):
            * `psr-4`: Must list the directory in `[]` so `["src/"]` for example.
            * `psr-4`: Must list your namespace first before the `"App\\"` and `"App\\Tests\\"` namespaces.
* Exclude `src/Entities` from Symfony's service autoloading:
```yaml
# config/services.yaml

# ...

services:

    # ...

    App\:
        # ...
        exclude: '../src/{Entities,Entity,Migrations,Tests,Kernel.php}'

# ...
```
* Setup `doctrine-static-meta` dependency injection by adding the following to `src/Kernel.php`:
```php
    // ...
    
    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        // ...
        
        $this->addDsmServices($container);
    }

    /**
     * @param ContainerBuilder $container
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    protected function addDsmServices(ContainerBuilder $container)
    {
        (new Container())->addConfiguration($container, $_SERVER);
    }
```