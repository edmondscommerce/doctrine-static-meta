# Background

I love Doctrine and think it's a great library, however I am not a huge fan of annotations. I think they are great at the start of a project, but once things get more complicated, or you start to refactor, then they can quickly become more of a hindrance than a help.

I think annotation can and should be used for metadata, but not for logic. When generating meta data for Doctrine, it can quite quickly become quite complex, especially if you want to diverge from the defaults.

Doctrine does offer ~~yaml and~~ xml based configuration, however I do really like the idea of all the code and configuration being in one place as achieved by annotations.

What I discovered is that there is another option for meta data call the [Static Function](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/php-mapping.html#static-function). This is a pure PHP solution in which each Entity class has a static `public static function loadMetadata(ClassMetadata $metadata)` which takes the metadata as an input and then adds to that the meta data for the Entity.

Exactly how you provide this metadata is userland and this library proposes a way to do that.

## Code Generation

Since the initial prototyping versions, this library has matured also into quite an extensive code generation library. The reason for using code generation is that there is a large amount of very deterministic boiler plate code that is required when putting together a complex domain that you wish to be properly type hinted.

Also, we are trying to move away from the idea that the EntityManager is a service locator and instead use defined Factories, Repositories and Savers that can be easily hinted for in your objects and brought in as dependency injected services.

The most recent versions now include DataTransfer objects which allow you to manipulate entity data freely whilst not sacrificing the validity of real Entity data. 

## Traits

A major feature of this library is extensive us of [Traits](http://php.net/manual/en/language.oop5.traits.php). This means that at first glance, the Entity objects can look very sparse. This is due to the fact that as much code as possible resides in reusable traits which the Entity `use`s

## DoctrineStaticMeta object

This is the object that handles the creation and provision of meta data for each Doctrine Entity.

You can see the object here: [DoctrineStaticMeta.php](./../src/DoctrineStaticMeta.php)

This is done by pulling out methods with a defined prefix: `getPropertyDoctrineMetaFor`. In this method, the entity is then able to provide meta data for one or more properties.

In this method, we then reflect on the Entity class and pull out static methods for generating property meta data. This is done by pulling out methods with a defined prefix: `getPropertyDoctrineMetaFor`. In this method, the entity is then able to provide meta data for one or more properties.

This concept then enables us to make extensive use of traits for properties, as the meta data does not have to be hard coded but can be dynamic. 

Also in the UsesPhpMetaData trait we have public static methods for `getSingular` and `getPlural` and these are then what is used in the dynamic meta data, to reference the Entity that is implementing the trait.

## UsesPHPMetaData Trait

The main concepts of this library hinge around Entities implementing the Trait [UsesPHPMetaData](./../src/Entity/Traits/UsesPHPMetaDataTrait.php)

In this trait we hook into the Static PHP Driver by exposing a public static method `loadMetadata`.

In this method, we then create or retrieve the static instance of DoctrineStaticMeta for the Entity class and pull out static methods for generating property meta data. 

## Field Traits

Fields can optionally be defined as Traits and Interfaces.

There is a generator and command to support easily creating these. The field traits also implement Symfony Validator meta data so that fields can be created that implement Doctrine and PHP types and validate with Symfony validators.

## Relation Traits and Interfaces

Finally, we are able to handle the relationship between Entities by using Traits. 

To see how this works, it is suggest you have a look through some of the [example projects](https://github.com/edmondscommerce/doctrine-static-meta-example)
