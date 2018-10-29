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

## Test Types

We have ditched the traditional unit,integration and functional naming convention as their definitions and rules are contentious and unclear.

Instead we use small, medium and large, as inspired by [this blog post](https://testing.googleblog.com/2010/12/test-sizes.html)

We also have a category for acceptance testing which covers the kind of testing a client might perform manually to accept the project.

The tests are defined by what is NOT allowed to be done. 

* The test should always be in the smallest name possible

* For each class and method, there should always be at least some small tests, for example testing what happens when invalid parameters are fed in.

* All tests should be run in a totally clean environment, either with tests cleaning up after themselves or the test environment being cleaned and rebuilt before each test

The time limits on small and medium tests are configured in the phpunit.xml and require methods to have the `@small` or `@medium` annotation

### Small

Should be very fast and test a small piece of functionality, eg a single method.

The test and the code being tested can not:

* make any network or web requests
* access a database
* access a caching system (eg Redis)
* write any changes to the filesystem

A single small test should execute in less than **1 second**

Small tests should have no side effects or cleanup required

You must annotate all your small tests with a `@small` annotation

### Medium

Medium test are generally a bit slower and have fewer restrictions

They can:

* access the database (localhost or not)
* access the filesystem 
* access caching services (localhost or not)

They can not:

* access anything on the network that is not localhost

A single medium test should execute in less than **5 seconds**

You must annotate all your small tests with a `@medium` annotation

### Large

Large tests have no restrictions and can run for an effectively unlimited amount of time, though of course you should always try to make your tests as fast and efficient as possible.

By default, the PHPQA configuration is 300 seconds which should be more than enough for a single test to run.

They can directly access as much or as little of the code as required.

Generally they are testing large pieces of functionality as a whole.

## Run Tests

```bash
bin/qa
```

This uses phpqa to run a full suite of tests including the PHPUnit tests

This will create another project in `/tmp/doctrine-static-meta-test-project/`

## Testing Generated Code

Extensive functional tests of the generated code are performed in [tests/Large/FullProjectBuildLargeTest.php](./../tests/Large/A/FullProjectBuildLargeTest.php)

## Set up Pre Commit Hook

To set up the pre commit hook, run:

```bash
#cd to project root
cd /var/www/project/root

#cd to hooks folder
cd .git/hooks/

#create relative symlink
ln -s ../../vendor/edmondscommerce/phpqa/gitHooks/pre-commit.bash pre-commit
```

Then ensure the hook is executable by running it:

``` bash
#cd to project root
cd /var/www/project/root

./git/hooks/pre-commit
```

And you should then see 

```
===========================================
PHPQA Pre Commit Hook
===========================================
```



