# Creating New Archetype Fields

## Running the command

The command `bin/doctrine dsm:generate:field` is used to create new archetype fields.

There are two required fields `-f|--field-fully-qualified-name` and `-y|--field-property-doctrine-type`.

`-f` is the fully qualified class name of the new archetype field which should end in `FieldTrait`.

`-y` is the type of the field, this can be a scalar type or another archetype field.

i.e.

`bin/doctrine dsm:generate:field -f 'EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Numeric\IntegerFieldTrait' -y int`

This will create two new files, the Archetype Field trait found at the `src/Entity/Fields/Interfaces/Numeric/IntegerFieldTrait.php/` and the interface found at `src/Entity/Fields/Interfaces/Numeric/IntegerFieldInterface.php` 

## Editing the new Archetype Field

### The Trait

This is where you can edit the Archetype Field meta and private functions.

### The Interface

The interface is where you set the default value and specify the return type for the public getter.

## Adding tests for the new Archetype Field

### The Trait Test

You should take a look at the tests for similar traits when writing you test. They extend from `EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\AbstractFieldTraitTest` which has a number of common tests such as checking for valid data.

To test a trait you might need a faker data provider, when you make this you will need to add it to `\EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityTestInterface::FAKER_DATA_PROVIDERS`.