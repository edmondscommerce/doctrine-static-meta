# Getting Started

This document describes how to get started with a DSM based Entities project

The document assumes starting from a completely clean slate.

## Set up Composer Dependencies

```
composer require edmondscommerce/doctrine-static-meta
```

## Create Project Directory Structure:

The next thing to do is to create the project directory structure.

Assuming the project is located in `/var/www/vhosts/myproject`

```bash
cd /var/www/vhosts/myproject
mkdir -p src/Entities
mkdir -p src/EntityRepositories
mkdir -p src/EntityRelations
mkdir -p tests
mkdir -p cache/Proxies
```

## Create your Database

Of course, you need a database to work with

```bash
dbName="myproject"
mysql -e "DROP DATABASE IF EXISTS $dbName "
mysql -e "CREATE DATABASE $dbName CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci"
```

## `.env` Config

You also need to specify credentials for the database in your `.env` file

The file should look something like this:

```
dbUser=dbusername
dbPass=pass
dbName=myproject
dbHost=localhost
```


## Set up the `cli-config.php` File

Doctrine's command line tools require a file called `cli-config.php` in the root of your project.

You can quickly set this up by copying the one from the `/vendor/edmondscommerce/doctrine-static-meta` project

```bash
cd /var/www/vhosts/myproject
cp vendor/edmondscommerce/doctrine-static-meta/cli-config.php .
```

Once this is in place, you should be able to call Doctrine commands.

```bash
./bin/doctrine
```

And see output like:

```
# bin/doctrine
Doctrine Command Line Interface 2.7.0-DEV

Usage:
  command [options] [arguments]

Options:
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  help                               Displays help for a command
  list                               Lists commands
 dbal
  dbal:import                        Import SQL file(s) directly to Database.
  dbal:reserved-words                Checks if the current database contains identifiers that are reserved.
  dbal:run-sql                       Executes arbitrary SQL directly from the command line.
 dsm
  dsm:generate:entity                Generate an Entity
  dsm:generate:field                 Generate a field
  dsm:generate:relations             Generate relations traits for your entities. Optionally filter down the list of entities to generate relationship traits for
  dsm:set:field                      Set an Entity as having a Field
  dsm:set:relation                   Set a relation between 2 entities. The relation must be one of EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator::RELATION_TYPES
 orm
  orm:clear-cache:metadata           Clear all metadata cache of the various cache drivers
  orm:clear-cache:query              Clear all query cache of the various cache drivers
  orm:clear-cache:region:collection  Clear a second-level cache collection region
  orm:clear-cache:region:entity      Clear a second-level cache entity region
  orm:clear-cache:region:query       Clear a second-level cache query region
  orm:clear-cache:result             Clear all result cache of the various cache drivers
  orm:convert-d1-schema              [orm:convert:d1-schema] Converts Doctrine 1.x schema into a Doctrine 2.x schema
  orm:convert-mapping                [orm:convert:mapping] Convert mapping information between supported formats
  orm:ensure-production-settings     Verify that Doctrine is properly configured for a production environment
  orm:generate-entities              [orm:generate:entities] Generate entity classes and method stubs from your mapping information
  orm:generate-proxies               [orm:generate:proxies] Generates proxy classes for entity classes
  orm:generate-repositories          [orm:generate:repositories] Generate repository classes from your mapping information
  orm:info                           Show basic information about all mapped entities
  orm:mapping:describe               Display information about mapped objects
  orm:run-dql                        Executes arbitrary DQL directly from the command line
  orm:schema-tool:create             Processes the schema and either create it directly on EntityManager Storage Connection or generate the SQL output
  orm:schema-tool:drop               Drop the complete database schema of EntityManager Storage Connection or generate the corresponding SQL output
  orm:schema-tool:update             Executes (or dumps) the SQL needed to update the database schema to match the current mapping metadata
  orm:validate-schema                Validate the mapping files


```

## Setup PHPUnit Configuration

We need to specify a PHPUnit Configuration that will bring in the required bootstrap file.

```bash
cd /var/www/vhosts/myproject
cp vendor/edmondscommerce/doctrine-static-meta/phpunit.xml .

```

## Creating Entities

You are now ready to start creating Entities.

You can do this manually if you prefer, though the command line generation is quick, accurate and easy.

```
# bin/doctrine dsm:generate:entity --help
Description:
  Generate an Entity

Usage:
  dsm:generate:entity [options]

Options:
  -f, --entity-fully-qualified-name=ENTITY-FULLY-QUALIFIED-NAME  The fully qualified name of the entity you want to create
  -u, --uuid-primary-key                                         Use a UUID in place of the standard primary key
  -c, --entity-specific-saver                                    Generate an implmentation of SaverInterface just for this entity
  -p, --project-root-path[=PROJECT-ROOT-PATH]                    the filesystem path to the folder for the project. This would be the folder that generally has a subfolder `src` and a sub folder `tests` [default: "/tmp/dsm/test-project"]
  -r, --project-root-namespace=PROJECT-ROOT-NAMESPACE            The root namespace for the project for which you are building entities. The entities root namespace is suffixed to the end of this [default: "My\GeneratedCodeTest\Project"]
  -s, --src-sub-folder=SRC-SUB-FOLDER                            The name of the subdfolder that contains sources. Generally this is `src` which is the default [default: "src"]
  -t, --test-sub-folder=TEST-SUB-FOLDER                          The name of the subdfolder that contains tests. Generally this is `tests` which is the default [default: "tests"]
  -h, --help                                                     Display this help message
  -q, --quiet                                                    Do not output any message
  -V, --version                                                  Display this application version
      --ansi                                                     Force ANSI output
      --no-ansi                                                  Disable ANSI output
  -n, --no-interaction                                           Do not ask any interactive question
  -v|vv|vvv, --verbose                                           Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug


```

If you have a few entities to create, you can use a simple BASH loop to do this:

```bash
#specify a root namespace variable to make life easy
rootNs="My\Test\Project\Entities\\"

#specify a simple line separated list of fully qualified names for Entities
entitiesToBuild="
${rootNs}Address
${rootNs}Customer
${rootNs}Customer\Segment
${rootNs}Customer\Category
${rootNs}Order
${rootNs}Order\Address
${rootNs}Order\LineItem
${rootNs}Product
${rootNs}Product\Brand
"

#then use a basic BASH for loop to iterate over these
for entity in $entitiesToBuild
do
    ./bin/doctrine dsm:generate:entity --entity-fully-qualified-name="$entity"
done
```

Please note that you may only have singular entity names like 'Client' or 'Company' and not plural names like 'Clients' and 'Companies'.

### Repositories

The Entity Generator will generate a repository for your Entity as well as the Entity Repository

### Test

The Entity Generator will generate a test class for your Entity that extends the [AbstractEntityTest](./../src/Entity/Testing/AbstractEntityTest.php)

This base test provides a reasonably thorough set of tests for any generated Entity. You should add extra business logic and validation testing in the generated test class.

### Savers

This generation will also generate the Entity Saver, if that is what you desire.

As standard there is a single generic [EntitySaver](./../src/Entity/Savers/EntitySaver.php) which will happily save any Entity that implements the [IdFieldInterface](./../src/Entity/Fields/Interfaces/PrimaryKey/IdFieldInterface.php).

If you have extra business logic that you want to include around saving then you can generate an EntitySpecificSaver by passing in the extra parameter, for example:

```bash
./bin/doctrine dsm:generate:entity --entity-fully-qualified-name="$entity" --entity-specific-saver
```

## Setting Relations Between Entities

Once you have built all your Entities, the next thing to do is to specify the relationships between them

For this you can again do it manually if you prefer, though the command line tool is quick, accurate and easy.

You have two basic ways of doing this, depending on how verbose you like to be in writing commands:

```bash
#full command with long options
phpNoXdebug ./bin/doctrine dsm:set:relation --entity1="${rootNs}Customer" --hasType=ManyToMany --entity2="${rootNs}Address"

#minimalist command with short options, note the shorthand syntax for specifying the command to run
phpNoXdebug ./bin/doctrine d:s:r -m "${rootNs}Customer" -t ManyToMany -i "${rootNs}Customer\Segment"
```

## Building Field Interfaces and Traits

Fields are comprised of Interfaces and Traits which are then implemented and used in Entity classes respectively.

DSM comes with some standard library fields which you can find in [src/Entity/Fields](./../src/Entity/Fields)

You can generate your own Fields with the generate field command:

```
# bin/doctrine dsm:generate:field --help
  Description:
    Generate a field
  
  Usage:
    dsm:generate:field [options]
  
  Options:
    -f, --field-fully-qualified-name=FIELD-FULLY-QUALIFIED-NAME      The fully qualified name of the property you want to generate
    -d, --field-property-doctrine-type=FIELD-PROPERTY-DOCTRINE-TYPE  The data type of the property you want to generate
    -z, --not-nullable                                               This field will not be nullable
    -u, --is-unique                                                  This field is unique, duplicates are not allowed
    -p, --project-root-path[=PROJECT-ROOT-PATH]                      the filesystem path to the folder for the project. This would be the folder that generally has a subfolder `src` and a sub folder `tests` [default: "/tmp/dsm/test-project"]
    -r, --project-root-namespace=PROJECT-ROOT-NAMESPACE              The root namespace for the project for which you are building entities. The entities root namespace is suffixed to the end of this [default: "My\GeneratedCodeTest\Project"]
    -s, --src-sub-folder=SRC-SUB-FOLDER                              The name of the subdfolder that contains sources. Generally this is `src` which is the default [default: "src"]
    -t, --test-sub-folder=TEST-SUB-FOLDER                            The name of the subdfolder that contains tests. Generally this is `tests` which is the default [default: "tests"]
    -h, --help                                                       Display this help message
    -q, --quiet                                                      Do not output any message
    -V, --version                                                    Display this application version
        --ansi                                                       Force ANSI output
        --no-ansi                                                    Disable ANSI output
    -n, --no-interaction                                             Do not ask any interactive question
    -v|vv|vvv, --verbose                                             Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```

### Nullable Fields
Fields are nullable by default, but you can mark a field as not nullable as required

### Unique Fields
You can mark a field as unique which will then generate a unique key on the Entity table.


### Contribute Back!!
If you make a Field that you think is good and likely to be generally useful then please do add it to the main library in [src/Entity/Fields](./../src/Entity/Fields) and then do a pull request.

Ideally each Field will have some tests that ensure that the validation etc is all working properly, though for now hte main library does not have this in place so it is not a requirement.


## Using a Build Script

It is highly recommended that you have a build script to build up your entities, relations, fields, embeddables

You have the choice of writing a BASH script which runs the commands, or writing a PHP script which interacts with the [Generator](./../src/CodeGeneration/Generator) objects directly.

**The recommended approach is to write your build script in PHP**

## Using Doctrine Migrations

You can use the command `php bin/doctrine migrations:diff` to generate a migration once you are happy with a build.

You need to check the results of this command to make sure the results will not cause problems i.e. data loss.

