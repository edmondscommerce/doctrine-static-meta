# Doctrine Static Meta
## By [Edmonds Commerce](https://www.edmondscommerce.co.uk)

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/00a50e56835f45b0ba32eed9c0285ede)](https://www.codacy.com/app/edmondscommerce/doctrine-static-meta?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=edmondscommerce/doctrine-static-meta&amp;utm_campaign=Badge_Grade) [![Build Status](https://travis-ci.org/edmondscommerce/doctrine-static-meta.svg?branch=master)](https://travis-ci.org/edmondscommerce/doctrine-static-meta)

An implementation of Doctrine using the [PHP Static Meta Data driver](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/php-mapping.html#static-function) and no annotations.

This library includes extensive traits and interfaces and also full code generation allowing you to set up a project quickly.

## Background

I love Doctrine and think it's a great library, however I am not a huge fan of annotations. I think they are great at the start of a project, but once things get more complicated, or you start to refactor, then they can quickly become more of a hindrance than a help.

Doctrine does offer yaml and xml based configuration, however I do really like the idea of all the code and configuration being in one place as achieved by annotations.

What I discovered is that there is another option for meta data call the [Static Function](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/php-mapping.html#static-function). This is a pure PHP solution in which each Entity class has a static `public static function loadMetadata(ClassMetadata $metadata)` which takes the metadata as an input and then adds to that the meta data for the Entity.

Exactly how you provide this metadata is userland and this library proposes a way to do that.

## Traits

A major feature of this library is extensive us of [Traits](http://php.net/manual/en/language.oop5.traits.php). For example have a look at the [Address](./example/project/src/Entities/Address.php) entity. It contains practically no native code, all functionality is being provided by Traits.

## UsesPHPMetaData Trait

The main concepts of this library hinge around Entities implementing the Trait [UsesPHPMetaData](./src/Entity/Traits/UsesPHPMetaData.php)

In this trait we hook into the Static PHP Driver by exposing a public static method `loadMetadata`.

In this method, we then reflect on the Entity class and pull out static methods for generating property meta data. This is done by pulling out methods with a defined prefix: `getPropertyMetaFor`. In this method, the entity is then able to provide meta data for one or more properties.

This concept then enables us to make extensive use of traits for properties, as the meta data does not have to be hard coded but can be dynamic. 

Also in the UsesPhpMetaData trait we have public static methods for `getSingular` and `getPlural` and these are then what is used in the dynamic meta data, to reference the Entity that is implementing the trait.

## Field Traits

The next aspect of this library is for there to be traits for each field that an Entity uses. This allows easy code reuse and refactoring. For example, each Entity should probably implement the [IdField](src/Entity/Traits/Fields/IdField.php) trait which sets up the primary key for the Entity.

## Relation Traits and Interfaces

Finally, we are able to handle the relationship between Entities by using Traits. For example the [Address](./example/project/src/Entities/Address.php) Entity has a relationship with the [Customer](example/project/src/Entities/Customer.php) Entity and this is defined by using the [HasCustomersInverseManyToMany](example/project/src/Entities/Relations/Customer/Traits/HasCustomers/HasCustomersInverseManyToMany.php) Trait.

We also use Interfaces such as [HasCustomers](example/project/src/Entities/Relations/Customer/Interfaces/HasCustomers.php) which describe generic methods and also give us something useful to `instanceof` with.

## Example Project

You can see a full example project in [example/project](example/project)

In particular, have a look at [example/build.bash](example/build.bash) which is what creates the example project and should give you a clear idea of how to start using the library.


## Further Reading

Have a look in the [docs](docs) Folder

### [Getting Started](./docs/Getting-Started.md)
### [Code Structure](./docs/Code-Structure.md)
### [Developing](./docs/Developing.md)
### [Working with Existing Database](./docs/Working-With-Existing-Database.md)
