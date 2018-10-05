# Doctrine Static Meta
## By [Edmonds Commerce](https://www.edmondscommerce.co.uk)

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/00a50e56835f45b0ba32eed9c0285ede)](https://www.codacy.com/app/edmondscommerce/doctrine-static-meta?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=edmondscommerce/doctrine-static-meta&amp;utm_campaign=Badge_Grade) 
[![Build Status](https://travis-ci.org/edmondscommerce/doctrine-static-meta.svg?branch=master)](https://travis-ci.org/edmondscommerce/doctrine-static-meta)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/edmondscommerce/doctrine-static-meta/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/edmondscommerce/doctrine-static-meta/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/edmondscommerce/doctrine-static-meta/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/edmondscommerce/doctrine-static-meta/?branch=master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/edmondscommerce/doctrine-static-meta/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
[![Maintainability](https://api.codeclimate.com/v1/badges/fd4655978dc2137dd375/maintainability)](https://codeclimate.com/github/edmondscommerce/doctrine-static-meta/maintainability)

An implementation of Doctrine using the [PHP Static Meta Data driver](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/php-mapping.html#static-function) and no annotations.

This library includes extensive traits and interfaces and also full code generation allowing you to set up a project quickly.

## Install

```bash
composer require edmondscommerce/typesafe-functions dev-master@dev
composer require edmondscommerce/doctrine-static-meta dev-master@dev
```

## Limitations

Whilst this is now at a stage where we are using it in production, it is still a work in progress.

* Currently we have only targeted MySQL

## Faker Fork

Please note, you need to use our fork of Faker with this library. We will try get this merged into Faker main at some point soon

```json
{
  "require": {
    "edmondscommerce/doctrine-static-meta": "dev-master@dev",
    "edmondscommerce/typesafe-functions": "dev-master@dev",
    "php": ">=7.2"
  },
  "require-dev": {
    "fzaninotto/faker": "dev-dsm-patches@dev",
    "edmondscommerce/phpqa": "^1.0"
  },
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/edmondscommerce/Faker.git"
    }
  ],
  "autoload": {
    "psr-4": {
      "My\\Test\\Project\\": [
        "src/"
      ]
    }
  },
  "autoload-dev": {
    "psr-4": {
      "My\\Test\\Project\\": [
        "tests/"
      ]
    }
  },
  "config": {
    "bin-dir": "bin",
    "preferred-install": {
       "edmondscommerce/*": "source",
       "fzaninotto/faker": "source",
       "*": "dist"
     },
    "optimize-autoloader": true
  }
}


```

## Further Reading

Have a look in the [docs](docs) Folder
### [Background](./docs/Background.md)
### [Getting Started](./docs/Getting-Started.md)
### [Code Structure](./docs/Code-Structure.md)
### [Developing](./docs/Developing.md)
### [Working with Existing Database](./docs/Working-With-Existing-Database.md)
### [Testing Your Project](./docs/Testing-Your-Project.md)
### [Embeddables](./docs/Embeddables.md)
