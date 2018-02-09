# Working with an Existing Database

If you are building Entity code to work with an existing database structure then here are some ideas on how you might do this.

## Basic Strategy

The basic strategy here is going to be to create a new database and then migrate our legacy database into it. This provides us with the opportunity to improve the schema and also remains non-destructive and easy to repeat until we have it working perfectly.

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
