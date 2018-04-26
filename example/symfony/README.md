# Symfony

This document details how to get DSM working with Symfony 4

## Setup

* Install symfony 4 [docs](https://symfony.com/doc/current/setup.html)
* Install doctrine-static-meta [docs](../../docs/Getting-Started.md)
    * Current gotchas:
        * [`composer.json`](./composer.json):
            * `psr-4`: Must list the directory in `[]` so `["src/"]` for example.
            * `psr-4`: Must list your namespace first before the `"App\\"` and `"App\\Tests\\"` namespaces.