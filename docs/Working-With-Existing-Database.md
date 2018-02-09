# Working with an Existing Database

If you are building Entity code to work with an existing database structure then here are some ideas on how you might do this.

## Basic Strategy

The basic strategy here is going to be to create a new database and then migrate our legacy database into it. 

This provides us with the opportunity to improve the schema and also remains non-destructive and easy to repeat until we have it working perfectly.

### Code Generation

The strategy includes extensive use of PHP code generation. This functionality is provided by this library:

https://php-code-generator.readthedocs.io/en/latest/index.html

You should thoroughly read these docs before continuing.

## First, Tidy up Names

The first thing I would suggest you do is to update the table names to represent the code namespace schema you would expect.

You should use underscores to represent the `\` character in the namespace:.

For example

`customer_order`

Would then represent the Entity

`My\Project\Entities\Customer\Order`

### Edit the Source Database

You can do this by updating the source database, which is mildly destructive

```sql
RENAME TABLE `old_name` `new_name`;
ALTER TABLE `new_name` DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Transform this Programmatically in your Build Script

The other, less destructive approach is to do this transform in your build script, for example using a `switch` statement.

```php
<?php
$tableName=$row['TABLE_NAME'];
switch($tableName){
    case 'old_name':
        $tableName='new_name';
        break;    
}
```

### Update Collation

Now is also a good time to update the collation in the database to a modern standard.

Suggested is: ` CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci `

## Write a Build Script

To help you iterate on this quickly, I'd suggest you try to automate as much of the process as you can.

*_please note: This is a userland, the examples provided are for demonstration purposes only_*

### Creating Entities from a MySQL Query

You might want create entities directly from a pre existing database. 

You can use something like the following technique to allow you to easily create entities from a query.

**_NOTE: this is an example, you should create your own to your requirements_**

```php
<?php declare(strict_types=1);

use Doctrine\ORM\EntityManager;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Container;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;

require __DIR__.'/vendor/autoload.php';

set_error_handler(
    function ($severity, $message, $file, $line)
    {
        if (!(error_reporting() & $severity)) {
            // This error code is not included in error_reporting
            return;
        }
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
);
try {
    SimpleEnv::setEnv(__DIR__.'/.env');
    $container = new Container();
    $container->buildSymfonyContainer($_SERVER);

    $projectNamespaceRoot = 'My\\Project\\';
    $entitiesFolderName = 'Entities';
    $entitiesNamespaceRoot = $projectNamespaceRoot.$entitiesFolderName.'\\';

    function getEntityFqnFromTableName(string $tableName, string $entitiesNamespaceRoot): string
    {
        $parts = explode('_', $tableName);
        $parts = array_map(
            function ($part)
            {
                return ucfirst(strtolower($part));
            },
            $parts
        );

        return $entitiesNamespaceRoot.implode("\\", $parts);
    }

    $entityGenerator = $container->get(EntityGenerator::class)
        ->setPathToProjectSrcRoot(
            $container->get(Config::class)::getProjectRootDirectory()
        )
        ->setProjectRootNamespace($projectNamespaceRoot)
        ->setEntitiesFolderName($entitiesFolderName)
        ->setTestSubFolderName('tests')
        ->setSrcSubFolderName('src');


    $con = $container->get(EntityManager::class)
        ->getConnection();
    $stmt = $con->query(
        "
    
SELECT
`TABLE_NAME`
FROM
  `INFORMATION_SCHEMA`.`TABLES` t

WHERE t.TABLE_SCHEMA='my_project_database'
    
    "
    );

    while ($row = $stmt->fetch()) {
        $entity = getEntityFqnFromTableName($row['TABLE_NAME'], $entitiesNamespaceRoot);
        echo "\n$entity\n";
        $entityGenerator->generateEntity(
            $entity
        );
    }

    $container->get(EdmondsCommerce\DoctrineStaticMeta\Schema\Schema::class)->validate()->update();

} catch (\Throwable $e) {
    throw new \RuntimeException('error building:' .$e->getMessage(), $e->getCode(), $e);
}

```

### Automatically Creating Entity Scalar Fields

The next step once the basic Entities are being created to your satisfaction is to start adding the fields in


#### Data Types
First of all, find out what data types you are using in your database:

```sql
SELECT DISTINCT 
 INFORMATION_SCHEMA.COLUMNS.DATA_TYPE 
FROM 
 INFORMATION_SCHEMA.COLUMNS 
WHERE
 INFORMATION_SCHEMA.COLUMNS.TABLE_SCHEMA = 'my_project_database'
```

You need to handle each of the data types listed.

#### Example Build Script

**_NOTE: this is an example, you should create your own to your requirements_**

```php
<?php declare(strict_types=1);

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\Container;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;
use gossi\codegen\generator\CodeFileGenerator;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpConstant;
use gossi\codegen\model\PhpMethod;
use gossi\codegen\model\PhpParameter;
use gossi\codegen\model\PhpProperty;

require __DIR__.'/vendor/autoload.php';

set_error_handler(
/** @noinspection MoreThanThreeArgumentsInspection */
    function ($severity, $message, $file, $line)
    {
        if (!(error_reporting() & $severity)) {
            // This error code is not included in error_reporting
            return;
        }
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
);
try {
    SimpleEnv::setEnv(__DIR__.'/.env');
    $container = new Container();
    $container->buildSymfonyContainer($_SERVER);


    $projectNamespaceRoot = 'My\\Project\\';
    $entitiesFolderName = 'Entities';
    $entitiesNamespaceRoot = $projectNamespaceRoot.$entitiesFolderName.'\\';
    $newDbName = $container->get(Config::class)->get(ConfigInterface::PARAM_DB_NAME);
    $legacyDbName = 'my_project_database';

    $namespaceHelper = $container->get(NamespaceHelper::class);


    function getEntityFqnFromTableName(string $tableName): string
    {
        $parts = explode('_', $tableName);
        $parts = array_map(
            function ($part)
            {
                return ucfirst(strtolower($part));
            },
            $parts
        );

        return implode("\\", $parts);
    }

    function getPropertyNameFromTableColumn(string $columnName)
    {
        $parts = explode('_', $columnName);
        $parts = array_map(
            function ($part)
            {
                return ucfirst(strtolower($part));
            },
            $parts
        );

        return lcfirst(implode('', $parts));
    }

    /**
     * @var $entityGenerator EntityGenerator
     */
    $entityGenerator = $container->get(EntityGenerator::class)
        ->setPathToProjectSrcRoot(
            $container->get(Config::class)::getProjectRootDirectory()
        )
        ->setProjectRootNamespace($projectNamespaceRoot)
        ->setEntitiesFolderName($entitiesFolderName)
        ->setTestSubFolderName('tests')
        ->setSrcSubFolderName('src');


    $dbConnection = $container->get(EntityManager::class)
        ->getConnection();
    $tableStmt = $dbConnection->query(
        <<<MYSQL
SELECT
`TABLE_NAME`
FROM
  `INFORMATION_SCHEMA`.`TABLES` t

WHERE t.TABLE_SCHEMA='${legacyDbName}'
       
MYSQL
    );

    $foreignKeyStmt = $dbConnection->prepare(
        <<<MYSQL
SELECT * 
FROM information_schema.KEY_COLUMN_USAGE 
WHERE 
REFERENCED_TABLE_SCHEMA='${legacyDbName}'
AND TABLE_NAME=?
AND COLUMN_NAME=?
MYSQL
    );

    $generator = new CodeFileGenerator(
        [
            'generateDocblock' => true,
            'declareStrictTypes' => true,
            'generateScalarTypeHints' => true,
            'generateReturnTypeHints' => true,
        ]
    );

    while ($table = $tableStmt->fetch()) {
        $fieldConstants = [];
        $entityFqn = $entitiesNamespaceRoot.getEntityFqnFromTableName($table['TABLE_NAME']);
        echo "\n$entityFqn\n";
        try {
            $entityFilePath = $entityGenerator->generateEntity(
                $entityFqn
            );
            $entityClass = PhpClass::fromFile($entityFilePath);
        } catch (\Exception $e) {
            throw new \RuntimeException(
                'Exception reading the entity class '.$entityFilePath.': '.$e->getMessage(),
                $e->getCode(),
                $e
            );
        }
        $describeStmt = $dbConnection->query("describe $legacyDbName.".$table['TABLE_NAME']);

        while ($field = $describeStmt->fetch()) {

            if ($field['Key'] === 'PRI') {
                continue;
            }
            $foreignKeyStmt->execute([$table['TABLE_NAME'], $field['Field']]);
            if (!empty($foreignKeyStmt->fetch())) {
                continue;
            }

            switch (true) {
                case 0 === strpos($field['Type'], 'int'):
                    $phpType = 'int';
                    $mappingHelperType = MappingHelper::TYPE_INTEGER;
                    break;
                case 0 === strpos($field['Type'], 'varchar'):
                    $phpType = 'string';
                    $mappingHelperType = MappingHelper::TYPE_STRING;
                    break;
                case 0 === strpos($field['Type'], 'text'):
                case 0 === strpos($field['Type'], 'longtext'):
                    $phpType = 'string';
                    $mappingHelperType = MappingHelper::TYPE_TEXT;
                    break;
                case 0 === strpos($field['Type'], 'date'):
                case 0 === strpos($field['Type'], 'time'):
                    $phpType = '\\'.\DateTime::class;
                    $mappingHelperType = MappingHelper::TYPE_DATETIME;
                    break;
            }

            try {
                $propertyName = getPropertyNameFromTableColumn($field['Field']);

                $property = PhpProperty::create($propertyName);
                $property->setType($phpType);
                $property->setVisibility('protected');
                $property->generateDocblock();
                $entityClass->setProperty($property);

                $getter = PhpMethod::create('get'.ucfirst($propertyName));
                $getter->setBody('return $this->'.$propertyName.';');
                $getter->setVisibility('public');
                $getter->generateDocblock();
                $getter->setType($phpType);
                $entityClass->setMethod($getter);

                $setter = PhpMethod::create('set'.ucfirst($propertyName));
                $setter->addParameter(
                    PhpParameter::create($propertyName)
                        ->setType($phpType)
                );
                $setter->setBody('$this->'.$propertyName.' = $'.$propertyName.';'."\n\n".'return $this;');
                $setter->setVisibility('public');
                $setter->generateDocblock();
                $setter->setType($namespaceHelper->basename($entityFqn));
                $entityClass->setMethod($setter);

                $constName = 'FIELD_'.strtoupper($propertyName).'_TYPE';
                $const = PhpConstant::create($constName)->setValue($mappingHelperType);
                $entityClass->setConstant($const);
                $fieldConstants[$propertyName] = $constName;

            } catch (\Throwable $e) {
                throw new \RuntimeException(
                    'Got an error when working on entity class path '
                    .$entityFilePath.': '.$e->getMessage(),
                    $e->getCode(),
                    $e
                );
            }
        }
        $constArray = PhpConstant::create('SCALAR_FIELDS_TO_TYPES');
        $constArrayExpression = '[';
        foreach ($fieldConstants as $propertyName => $constName) {
            $constArrayExpression .= "\n   '$propertyName'=> self::".$constName.',';
        }
        $constArrayExpression .= "\n]";
        $constArray->setExpression($constArrayExpression);
        $entityClass->setConstant($constArray);

        $metaDataForFields = PhpMethod::create(
            UsesPHPMetaDataInterface::METHOD_PREFIX_GET_PROPERTY_META.'ScalarFields'
        );
        $metaDataForFields->setStatic(true);
        $metaDataForFields->addParameter(
            PhpParameter::create('builder')
                ->setType(
                    'ClassMetadataBuilder'
                )
        );
        $metaDataForFields->setBody('MappingHelper::setSimpleFields(self::SCALAR_FIELDS_TO_TYPES, $builder);');
        $entityClass->setMethod($metaDataForFields);

        $entityClass->addUseStatement('\\'.MappingHelper::class);
        $entityClass->addUseStatement('\\'.ClassMetadataBuilder::class);
        $generated = $generator->generate($entityClass);
        file_put_contents($entityFilePath, $generated);
    }
    echo "\n\nValidating and Updating the Database\n\n";
    $container->get(EdmondsCommerce\DoctrineStaticMeta\Schema\Schema::class)
        ->validate()
        ->update();


} catch (\Throwable $e) {
    die(
        'error in build.php: '.$e->getMessage()."\n\n\n".$e->getTraceAsString()
    );
}


```

#### Post Processing Generated Code

It's a huge time saver but it's not going to come out perfect.

I'd suggest using other tools to autoformat and fix issues with the generated code in accordance with your preferred coding standards.

The [EA Extended Validations](https://plugins.jetbrains.com/plugin/7622-php-inspections-ea-extended-) plugin for PHPStorm is great for this, as is the [phpqa](https://github.com/edmondscommerce/phpqa) library

### Relations

We also need to look at existing relations

To view existing relations in the database, run

```sql
SELECT 
 * 
FROM 
 INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE 
 REFERENCED_TABLE_SCHEMA = 'my_project_database'
```

Programmatically calculating exactly what these relationship mean, and exactly how they should be configured as entities is quite complex and error prone.

I've decided that for now it's better to do this part semi automatically.

We will sort out the foreign keys and then will use this to decide what relations are in place.


#### Renaming Foreign Keys

If you renamed your tables in your source database, then you probably now have foreign key columns that no longer make sense and need to be updated to keep things understandable.

Unfortunately you can't just rename a foreign key column, we have to drop the constraint, do the rename and the reinstate the constraint.

Here is a PHP snippet that will update all foreign key related columns to ensure they match up with the relation

**_WARNING: This is destructive, make sure your database is properly backed up!!!_**

```php
<?php

use Doctrine\ORM\EntityManager;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\Container;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;

require __DIR__.'/vendor/autoload.php';

set_error_handler(
/** @noinspection MoreThanThreeArgumentsInspection */
    function ($severity, $message, $file, $line)
    {
        if (!(error_reporting() & $severity)) {
            // This error code is not included in error_reporting
            return;
        }
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
);
try {
    SimpleEnv::setEnv(__DIR__.'/../.env');
    $container = new Container();
    $container->buildSymfonyContainer($_SERVER);
    $dbConnection = $container->get(EntityManager::class)
        ->getConnection();

    $projectNamespaceRoot = 'My\\Project\\';
    $entitiesFolderName = 'Entities';
    $entitiesNamespaceRoot = $projectNamespaceRoot.$entitiesFolderName.'\\';
    $newDbName = $container->get(Config::class)->get(ConfigInterface::PARAM_DB_NAME);
    $legacyDbName = 'my_project_database';

    $namespaceHelper = $container->get(NamespaceHelper::class);
} catch (\Throwable $e) {
    die('Error setting up Container: '.$e->getMessage()."\n\n".$e->getTraceAsString());
}

$incorrectColumnNamesStmt = $dbConnection->query(
    "
SELECT 
* 
FROM 
INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE 
REFERENCED_TABLE_SCHEMA = '$legacyDbName' 
AND COLUMN_NAME != CONCAT(REFERENCED_TABLE_NAME, '_', REFERENCED_COLUMN_NAME)
    "
);
while ($row = $incorrectColumnNamesStmt->fetch()) {
    $tableName = $row['TABLE_NAME'];
    $incorrectForeignKey = $row['CONSTRAINT_NAME'];
    $incorrectColumnName = $row['COLUMN_NAME'];
    $referencedTableName = $row['REFERENCED_TABLE_NAME'];
    $referencedColumnName = $row['REFERENCED_COLUMN_NAME'];
    $correctColumnName = $referencedTableName.'_'.$referencedColumnName;

    list($columnType, $nullable, $default) = array_values(
        $dbConnection->query(
            <<<MYSQL

select 
COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT 
from information_schema.COLUMNS 
where TABLE_SCHEMA = '$legacyDbName'
and TABLE_NAME='$tableName'
and COLUMN_NAME='$incorrectColumnName'

MYSQL

        )->fetch()
    );

    $nulType = $nullable ? ' NULL ' : ' NOT NULL ';

    $dbConnection->query(
        "
ALTER TABLE 
$legacyDbName.$tableName
DROP FOREIGN KEY `$incorrectForeignKey`
"
    );

    $dbConnection->query(
        "
ALTER TABLE $legacyDbName.$tableName 
CHANGE `$incorrectColumnName` `$correctColumnName` $columnType $nulType
        
        "
    );

    $dbConnection->query(
        "
ALTER TABLE $legacyDbName.$tableName 
ADD FOREIGN KEY (`$correctColumnName`) 
REFERENCES `$referencedTableName`(`$referencedColumnName`) 
ON DELETE RESTRICT ON UPDATE RESTRICT ;        
        "
    );

}
```

## Importing Data From Legacy into New

At this stage, we are happy that our new database and entity structure are as we want them to be. 

The goal here is to migrate legacy data into the new database.

This is a good time to decide to change things a bit, leave legacy data behind and also rework the relationships a bit.

### Migrating Scalar Data

The easiest bit to do is to migrate over the scalar data, including the Primary Key.


