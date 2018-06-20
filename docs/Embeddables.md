# Embeddables

Doctrine Static Meta has the concept of Embeddables which allow you to easily embed other objects into your Entities.

An example of this is the [MoneyEmbeddable](./../src/Entity/Embeddable/Objects/Financial/MoneyEmbeddable.php)

The embeddable consists of a few pieces of code:

## Anatomy

Here is a run down of the anatomy of a DSM Embeddable:

### 1. The Embeddable Object Itself

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

### 2. The Embeddable Object Interface

The Embeddable object should implement a defined interface, for example

[MoneyEmbeddableInterface.php](./../src/Entity/Embeddable/Interfaces/Objects/Financial/MoneyEmbeddableInterface.php)

### 3. The Embeddable Trait

As we do with [Fields](./../src/Entity/Fields), we have a Trait which is then used in your Entity class. This provides the correct meta data and the property to store the Embeddable instance against.

For example, [HasMoneyEmbeddableTrait.php](./../src/Entity/Embeddable/Traits/Financial/HasMoneyEmbeddableTrait.php)

### 4.The Embeddable Trait Interface

The methods of the trait should be reflected in an Interface which is implemented by the Entity

For example [HasMoneyEmbeddableInterface.php](./../src/Entity/Embeddable/Interfaces/Financial/HasMoneyEmbeddableInterface.php)

## Setting an Embeddable in your Entity

Depending on your build strategy, you have two choices on how to add an Embeddable to your Entity:

### Bash Command

You can use the bash command to assign an Embeddable to your Entity

```bash
./bin/doctrine dsm:set:embeddable \
    --project-root-path="/path/to/project" \
    --project-root-namespace="My/Project/Namespace" \
    --entity="My\Project\Namespace\Entities\Thing" \
    --embeddable="EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Financial\HasMoneyEmbeddableTrait" 
```

### PHP Script

If you are using a PHP build script (recommended) then you can interact with the generator directly:

```php
<?php
use \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable\EntityEmbeddableSetter;

$entityFqn='Your\\Project\\Entities\\Thing';
$embeddableTraitFqn=\EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Financial\HasMoneyEmbeddableTrait::class;

$generator=new EntityEmbeddableSetter(new CodeHelper(new NamespaceHelper()));
$generator->setEntityHasEmbeddable($entityFqn,$embeddableTraitFqn);

```

## Creating a new Embeddable in your Project based upon a DSM Embeddable

The standard library of Embeddables in DSM are OK, but you might want ot bring teh code into your own project so that you can change things like the property name, Doctrine meta data config etc.

This is fully supported with the Archetype based generation.

### Bash Command

You can use the bash command to assign an Embeddable to your Entity

```bash
./bin/doctrine dsm:set:embeddable \
    --project-root-path="/path/to/project" \
    --project-root-namespace="My/Project/Namespace" \
    --entity="My\Project\Namespace\Entities\Thing" \
    --embeddable="EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Financial\HasMoneyEmbeddableTrait" 
```

### PHP Script

If you are using a PHP build script (recommended) then you can interact with the generator directly:

```php
<?php
use \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable\EntityEmbeddableSetter;

$entityFqn='Your\\Project\\Entities\\Thing';
$embeddableTraitFqn=\EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Financial\HasMoneyEmbeddableTrait::class;

$generator=new EntityEmbeddableSetter(new CodeHelper(new NamespaceHelper()));
$generator->setEntityHasEmbeddable($entityFqn,$embeddableTraitFqn);

```
