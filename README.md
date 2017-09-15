# Doctrine Static Meta
## By [Edmonds Commerce](https://www.edmondscommerce.co.uk)

An implementation of Doctrine using the PHP Static Meta Data driver and no annotations

In the src folder you have some traits which are ready to use in your own project

There is also an ExampleEntities Folder which contains a small demo implementation

**_This is an idea rather than a fully complete solution_**

## Background

I love Doctrine and think it's a great library, however I am not a huge fan of annotations. I think they are great at the start of a project, but once things get more complicated, or you start to refactor, then they can quickly become more of a hindrance than a help.

Doctrine does offer yaml and xml based configuration, however I do really like the idea of all the code and configuration being in one place as achieved by annotations.

What I discovered is that there is another option for meta data call the [Static Function](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/php-mapping.html#static-function). This is a pure PHP solution in which each Entity class has a static `public static function loadMetadata(ClassMetadata $metadata)` which takes the metadata as an input and then adds to that the meta data for the Entity.

Exactly how you provide this metadata is userland and this library proposes a way to do that.

## Traits

A major feature of this library is extensive us of [Traits](http://php.net/manual/en/language.oop5.traits.php). For example have a look at the [Person](example/ExampleEntities/Person.php) entity. It contains practically no native code, all functionality is being provided by Traits.

## UsesPHPMetaData Trait

The main concepts of this library hinge around Entities implementing the Trait [UsesPHPMetaData](src/Traits/UsesPHPMetaData.php)

In this trait we hook into the Static PHP Driver by exposing a public static method `loadMetadata`.

In this method, we then reflect on the Entity class and pull out static methods for generating property meta data. This is done by pulling out methods with a defined prefix: `getPropertyMetaFor`. In this method, the entity is then able to provide meta data for one or more properties.

This concept then enables us to make extensive use of traits for properties, as the meta data does not have to be hard coded but can be dynamic. 

Also in the UsesPhpMetaData trait we have public static methods for `getSingular` and `getPlural` and these are then what is used in the dynamic meta data, to reference the Entity that is implementing the trait.

## Field Traits

The next aspect of this library is for there to be traits for each field that an Entity uses. This allows easy code reuse and refactoring. For example, each Entity should probably implement the [IdField](src/Traits/Fields/IdField.php) trait which sets up the primary key for the Entity.

## Relation Traits

Finally, we are able to handle the relationship between Entities by using Traits. For example the [Person](example/ExampleEntities/Person.php) Entity has a relationship with the [Address](example/ExampleEntities/Properties/Address.php) Entity and this is defined by using the [HasAddresses](example/ExampleEntities/Traits/Relations/Properties/HasAddresses.php) Trait.

In the HasAddresses trait, we have the key method 
```php
   protected static function getPropertyMetaForAddresses(ClassMetadataBuilder $builder)
    {
        $builder->addOwningManyToMany(
            Address::getPlural(),
            Address::class,
            static::getPlural()
        );
    }
```

This method sets up an [owning many to many](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#many-to-many-bidirectional) relationship from the Entity implementing the Trait (as referenced by use of `static::`) and the Address entity. As we are in pure PHP, we can totally avoid having to maintain a brittle string representation of the Address entity FQN, we can use the simple `Address::class` which is much more reliable and also easier to refactor in a modern IDE.

## Generating Example Schema

please look at [this bash script](example/createExampleDb.bash)

This assumes you are running a bash shell on a host that also has MySQL installed on localhost. You might want to edit some of the configuration values.

To create the example schema, simply run the bash script.

If you do not want to have to repeatedly put in your root password, you have the option of running

```bash

export rootPass="my root pass"
```
Which will then be used by the script without prompting for it

## Using in your own projects

To use this concept in your own projects, simply

```bash
composer require edmonds/doctrine-static-meta
```
And then create your entity classes and use the [UsesPHPMetaData.php](src/Traits/UsesPHPMetaData.php) Trait.

For scalar properties, you can simply declare the private properties and generate your getters and setters and then you must have a method beginning with 



