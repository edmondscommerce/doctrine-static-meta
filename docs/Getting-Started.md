# Getting Started

This document describes how to get started with a DSM based Entities project

The document assumes starting from a completely clean slate.

## Example Project

To make life a bit easier, there is an [example project](./../example/project) embedded in the library which you can refer to.

You can have a look at the [build.bash](./../example/build.bash) script to see how you might bootstrap your own project. 

## Set up Composer Dependencies

Here is an example `composer.json` file for a DSM project:

```json
{
  "require": {
    "edmondscommerce/doctrine-static-meta": "~1",
    "php": ">=7.1"
  },
  "require-dev": {
    "phpunit/phpunit": "^6.3",
    "fzaninotto/faker": "^1.7"
  },
  "autoload": {
    "psr-4": {
      "My\\Test\\Project\\": [
        "src/"
      ]
    }
  },
  "autoload-dev": {
    "psr-4": {
      "My\\Test\\Project\\": [
        "tests/"
      ]
    }
  },
  "config": {
    "bin-dir": "bin",
    "preferred-install": "dist",
    "optimize-autoloader": true
  }
}
```

If you paste the above into your composer.json and then run `composer install` then that will bring in the library and other dependencies.

## Create Project Directory Structure:

The next thing to do is to create the project directory structure.

Assuming the project is located in `/var/www/vhosts/myproject`

```bash
cd /var/www/vhosts/myproject
mkdir -p src/Entities
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
dbName=marketing
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
18:24 $ ./bin/doctrine
Doctrine Command Line Interface 2.6.0

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
  dsm:generate:relations             Generate relations traits for your entities. Optionally filter down the list of entities to generate relationship traits for
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

## Creating Entities

You are now ready to start creating Entities.

You can do this manually if you prefer, though the command line generation is quick, accurate and easy.

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

## Using a build script

You might decide that actually you would prefer to write a BASH build script to handle this for you so that you can easily test ideas, rip it down and rebuild.

For inspiration on how to do this, suggest looking at the aforementioned [build.bash](./../example/build.bash) script for the example project.
