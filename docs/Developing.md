# Developing on the Module

To develop on this module you should do the following:

## Clone the Repo

Simply clone this repo as normal

## Install Depenendencies

You need to composer install all the depenencies:

```bash
composer install --prefer-dist
```

## Create .env File

In the root of the project:

```bash
cp .env.dist .env

```

Then edit this file accordingly. You can remove the dbEntitiesPath field

## Create your database

using the same credentials as specified in you `.env` file:

```bash
mysql -e "CREATE DATABASE doctrine_static_example CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci"

```

## Run the Tests

```bash

bin/phpunit tests
```

This will create another project in `/tmp/doctrine-static-meta-test-project/`



## Running full test (need to commit changes)
