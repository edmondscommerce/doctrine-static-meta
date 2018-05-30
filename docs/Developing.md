# Developing on the Module

## Installation And Setup

To develop on this module you should do the following:

### Clone the Repo

Simply clone this repo as normal

## Install Depenendencies

You need to composer install all the depenencies:

```bash
composer install --prefer-dist
```

### Create .env File

In the root of the project:

```bash
cp .env.dist .env
```

Then edit this file accordingly. You can remove the dbEntitiesPath field

### Create your database

using the same credentials as specified in you `.env` file:

```bash
mysql -e "CREATE DATABASE doctrine_static_example CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci"

```

### Run Tests

```bash
bin/qa
```

This uses phpqa to run a full suite of tests including the PHPUnit tests

This will create another project in `/tmp/doctrine-static-meta-test-project/`

## Testing Generated Code

The basic tests for code generation are in [tests/integration/CodeGeneration/Generator](./../tests/integration/CodeGeneration/Generator)

These tests are primarily to ensure that the generation commands work and that the code is fundamentally sound. 

Extensive functional/integration tests of the generated code are performed in [tests/GeneratedCode/GeneratedCodeTest.php](./../tests/functional/FullProjectBuildIntegrationTest.php)




