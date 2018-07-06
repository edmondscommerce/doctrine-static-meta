# Doctrine Static Meta
## By [Edmonds Commerce](https://www.edmondscommerce.co.uk)

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/00a50e56835f45b0ba32eed9c0285ede)](https://www.codacy.com/app/edmondscommerce/doctrine-static-meta?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=edmondscommerce/doctrine-static-meta&amp;utm_campaign=Badge_Grade) 
[![Build Status](https://travis-ci.org/edmondscommerce/doctrine-static-meta.svg?branch=master)](https://travis-ci.org/edmondscommerce/doctrine-static-meta)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/edmondscommerce/doctrine-static-meta/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/edmondscommerce/doctrine-static-meta/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/edmondscommerce/doctrine-static-meta/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/edmondscommerce/doctrine-static-meta/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/edmondscommerce/doctrine-static-meta/badges/build.png?b=master)](https://scrutinizer-ci.com/g/edmondscommerce/doctrine-static-meta/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/edmondscommerce/doctrine-static-meta/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
[![Maintainability](https://api.codeclimate.com/v1/badges/fd4655978dc2137dd375/maintainability)](https://codeclimate.com/github/edmondscommerce/doctrine-static-meta/maintainability)

An implementation of Doctrine using the [PHP Static Meta Data driver](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/php-mapping.html#static-function) and no annotations.

This library includes extensive traits and interfaces and also full code generation allowing you to set up a project quickly.

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
    "php": ">=7.1"
  },
  "require-dev": {
    "fzaninotto/faker": "dev-dsm-patches@dev",
    "edmondscommerce/phpqa": "1.0.1"
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

## Background

I love Doctrine and think it's a great library, however I am not a huge fan of annotations. I think they are great at the start of a project, but once things get more complicated, or you start to refactor, then they can quickly become more of a hindrance than a help.

Doctrine does offer yaml and xml based configuration, however I do really like the idea of all the code and configuration being in one place as achieved by annotations.

What I discovered is that there is another option for meta data call the [Static Function](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/php-mapping.html#static-function). This is a pure PHP solution in which each Entity class has a static `public static function loadMetadata(ClassMetadata $metadata)` which takes the metadata as an input and then adds to that the meta data for the Entity.

Exactly how you provide this metadata is userland and this library proposes a way to do that.

## Traits

A major feature of this library is extensive us of [Traits](http://php.net/manual/en/language.oop5.traits.php). This means that at first glance, the Entity objects can look very sparse. This is due to the fact that as much code as possible resides in reusable traits which the Entity `use`s

## UsesPHPMetaData Trait

The main concepts of this library hinge around Entities implementing the Trait [UsesPHPMetaData](./src/Entity/Traits/UsesPHPMetaDataTrait.php)

In this trait we hook into the Static PHP Driver by exposing a public static method `loadMetadata`.

In this method, we then reflect on the Entity class and pull out static methods for generating property meta data. This is done by pulling out methods with a defined prefix: `getPropertyDoctrineMetaFor`. In this method, the entity is then able to provide meta data for one or more properties.
In this method, we then reflect on the Entity class and pull out static methods for generating property meta data. This is done by pulling out methods with a defined prefix: `getPropertyDoctrineMetaFor`. In this method, the entity is then able to provide meta data for one or more properties.

This concept then enables us to make extensive use of traits for properties, as the meta data does not have to be hard coded but can be dynamic. 

Also in the UsesPhpMetaData trait we have public static methods for `getSingular` and `getPlural` and these are then what is used in the dynamic meta data, to reference the Entity that is implementing the trait.

## Field Traits

Fields can optionally be defined as Traits and Interfaces.

There is a generator and command to support easily creating these. The field traits also implement Symfony Validator meta data so that fields can be created that implement Doctrine and PHP types and validate with Symfony validators.

## Relation Traits and Interfaces

Finally, we are able to handle the relationship between Entities by using Traits. 

To see how this works, it is suggest you have a look through some of the [example projects](https://github.com/edmondscommerce/doctrine-static-meta-example)

## Further Reading

Have a look in the [docs](docs) Folder

### [Getting Started](./docs/Getting-Started.md)
### [Code Structure](./docs/Code-Structure.md)
### [Developing](./docs/Developing.md)
### [Working with Existing Database](./docs/Working-With-Existing-Database.md)
### [Testing Your Project](./docs/Testing-Your-Project.md)
### [Embeddables](./docs/Embeddables.md)
