# Embeddables

Doctrine Static Meta has the concept of Embeddables which allow you to easily embed other objects into your Entities.

An example of this is the [](./../src/Entity/Embeddable/Objects/Financial/MoneyEmbeddable.php)

The embeddable consists of a few pieces of code:

## 1. The Embeddable Object

This is the actual standalone object, an instance of which becomes a property of your Entity.

The Embeddable must implement the `public static function loadMetadata(ClassMetadata $metadata): void` method.

It must mark itself as embeddable:

```php
<?php
/* ... */
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setEmbeddable();
```

And then it must declare the meta data for it's own properties in the normal way, for example:

```php
<?php
/* ... */
    MappingHelper::setSimpleFields(
            [
                MoneyEmbeddableInterface::EMBEDDED_PROP_CURRENCY_CODE => MappingHelper::TYPE_STRING,
                MoneyEmbeddableInterface::EMBEDDED_PROP_AMOUNT        => MappingHelper::TYPE_INTEGER,
            ], $builder
        );
```

The embeddable object should provide the required getters and setters and have it's own private properties in the normal Entity style.

### The Embeddable Object Interface

The Embeddable object should implement a defined interface, for example

[MoneyEmbeddableInterface.php](./../src/Entity/Embeddable/Interfaces/Objects/Financial/MoneyEmbeddableInterface.php)

## 2. The Embeddable Trait

As we do with [Fields](./../src/Entity/Fields), we have a Trait which is then used in your Entity class. This provides the correct meta data and the property to store the Embeddable instance against.

For example, [HasMoneyEmbeddableTrait.php](./../src/Entity/Embeddable/Traits/Financial/HasMoneyEmbeddableTrait.php)

### The Embeddable Trait Interface

The methods of the trait should be reflected in an Interface which is implemented by the Entity

For example [HasMoneyEmbeddableInterface.php](./../src/Entity/Embeddable/Interfaces/Financial/HasMoneyEmbeddableInterface.php)
