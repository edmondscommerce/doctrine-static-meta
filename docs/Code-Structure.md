#Code Structure
This document describes the structure and process of this library

There are a few main parts to this library:

* [Code Generation](../src/CodeGeneration)
* [Code Templates](./../codeTemplates)
* [Entity Traits and Interfaces](./../src/Entity)

Along with this there are some more optional elements such as [SimpleEnv](./../src/SimpleEnv.php) and the [DevEntityManagerFactory](./../src/EntityManager/DevEntityManagerFactory.php) which can easily be replaced with other components as required.

##Code Generation

The Code Generation can create Entity classes and also the traits and interfaces to manage relations between Entities.

The code generation works on the principal of taking the valid PHP code that resides in [Code Templates](./../codeTemplates) and then updating that to fit the newly created Entity.

There are currently three code generations that can be perfomed and for each of these we have a command:

* [Generate Entities](./../src/CodeGeneration/Command/GenerateEntityCommand.php)
* [Generate Relations](./../src/CodeGeneration/Command/GenerateRelationsCommand.php)
* [Set Relation](./../src/CodeGeneration/Command/SetRelationCommand.php)

### Generate Entities

To generate an Entity, we can use the [Command](./../src/CodeGeneration/Command/GenerateEntityCommand.php), or we can hook into the [Entity Generator](../src/CodeGeneration/Generator/EntityGenerator.php) object directly.

### Generate Relations

Currently, the system will generate all traits for all possible relations for the specified Entity. Whilst this is perhaps a bit wasteful and causes some bloat, it is simple.

It would certainly be possible to generate the relations on demand only, however this is not the case at the moment.

To generate relations for an Entity, we can either use the [Command](../src/CodeGeneration/Generator/EntityGenerator.php) or use the [Relations Generator](./../src/CodeGeneration/Generator/RelationsGenerator.php) object directly.

### Set Relations

The thing that glues the above concepts together, we can use generated relations to make relations, or associations, between 2 Entities.

To do this, we can either use the [Command](./../src/CodeGeneration/Command/SetRelationCommand.php) or we can use the [Relations Generator](./../src/CodeGeneration/Generator/RelationsGenerator.php) object directly

If the set relations command or method are called before Generate Relations, then the relations will be generated automatically.

##Code Templates

The code templates comprise two elements:
 
 * [src](./../codeTemplates/src)
 * [tests](./../codeTemplates/tests)

###src

For each Entity that is created, an Entity class and a test class are created.

Entities are created in an `Entities` namespace/folder. This defaults to `Entities` but can be configured as desired.

####TemplateEntity

The Entity class is based on [TemplateEntity](./../codeTemplates/src/Entities/TemplateEntity.php) which contains the most minimalist specification possible to work with the DSM package.

This includes:

* [Id Field](./../src/Entity/Traits/Fields/IdField.php)
* [UsesPHPMetaData](./../src/Entity/Traits/UsesPHPMetaData.php)

This means that the Entity has an ID field and also implements the methods required to build the metadata to be used by Doctrine's static PHP meta data driver.

###tests

The tests includes a EntityTest which is an empty extension of the [AbstractEntityTest](./../codeTemplates/tests/Entities/AbstractEntityTest.php) which is ensured to exist. 

This contains all of the basic logic to test an Entity. It will create the Entity and it's associations and will persist and then reload the Entity to ensure everything is working as it should. Extensive schema validation is performed.

The actual class test is initially empty, ready for you to add in any tests for your particular entity logic, or to override or disable the Abstract methods as you see fit.

The tests section also includes a basic PHPUnit [bootstrap.php](./../codeTemplates/tests/bootstrap.php) file which will drop and recreate the test database on each run of the tests.

## Entity Traits and Interfaces

The DSM library includes a selection of traits and interfaces, some of which are essential but most of which are simply useful.

###Id Field

As implemented in the [TemplateEntity](./../codeTemplates/src/Entities/TemplateEntity.php), 
the ID field uses the ID Field trait to implement a fairly standard Entity ID. This includes the field being named `id` and being defined as the primary key.

This is one of many standard [fields](./../src/Entity/Traits/Fields) included in the DSM library

###UsesPHPMetaData
This trait is the nucleus of this whole library. It implements the `public static function loadMetaData(ClassMetadata $metadata)` method which is called by the static PHP meta data driver.

In turn this method will instantiate an instance of `\Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder` and then use this to build meta data for properties and the class itself.

The way this works is by scanning the Entity methods for ones beginning with the prefix `\EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface::propertyMetaDataMethodPrefix` which is `'getPropertyMetaFor'`

This means that we can define methods as we see fit to provide meta data for the Entity. We can have a method per property, called `getPropertyMetatDataFor{PropertyName}`

Or we can have a method called `getPropertyMetaForScalarProperties` and then define all our property meta in one go. As the implementing developer, the choice is yours.

## Other Items of Note

Here are some other items of note:

###MappingHelper

One thing that you do have to play with when building class meta data in your `getPropertyMetaFor` methods is the [MappingHelper](./../src/MappingHelper.php) which can assist with quickly and easily setting up mapping for simple properties with scalar values, including:

* string
* int
* float
* decimal
* datetime

For example you might do:

```php
<?php declare(strict_types=1);

namespace My\DSM\Project;

use \EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

class MyEntity implements DSM\Interfaces\UsesPHPMetaDataInterface
{

    use DSM\Traits\UsesPHPMetaData;
    use DSM\Traits\Fields\IdField;

    /**
     * @var string
     */
    protected $stringVar = '';

    /**
     * @var float
     */
    protected $floatVar = 0.0;

    /**
     * @var \DateTime
     */
    protected $dateTimeVar;

    /**
     * load property meta data for all our simple scalar fields (and \DateTime)
     *
     * @param ClassMetadataBuilder $builder
     */
    protected static function getPropertyMetaForSimpleFields(ClassMetadataBuilder $builder)
    {
        MappingHelper::setSimpleFields(
            [
                'stringVar'   => MappingHelper::TYPE_STRING,
                'floatVar'    => MappingHelper::TYPE_FLOAT,
                'dateTimeVar' => MappingHelper::TYPE_DATETIME
            ],
            $builder
        );
    }

    /**
     * @return string
     */
    public function getStringVar(): string
    {
        return $this->stringVar;
    }

    /**
     * @param string $stringVar
     *
     * @return MyEntity
     */
    public function setStringVar(string $stringVar): MyEntity
    {
        $this->stringVar = $stringVar;
        return $this;
    }

    /**
     * @return float
     */
    public function getFloatVar(): float
    {
        return $this->floatVar;
    }

    /**
     * @param float $floatVar
     *
     * @return MyEntity
     */
    public function setFloatVar(float $floatVar): MyEntity
    {
        $this->floatVar = $floatVar;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateTimeVar(): \DateTime
    {
        return $this->dateTimeVar;
    }

    /**
     * @param \DateTime $dateTimeVar
     *
     * @return MyEntity
     */
    public function setDateTimeVar(\DateTime $dateTimeVar): MyEntity
    {
        $this->dateTimeVar = $dateTimeVar;
        return $this;
    }

    
}


```


###FileCreationTransaction

To assist with keeping track of files that are being generated, there is a [FileCreationTransaction](./../src/CodeGeneration/Generator/FileCreationTransaction.php) which has the sole job of keeping track of files that have been created and then upon an error, echoing out a find command that will allow you to easily find and remove created files.


