# Doctrine Static Meta
## By [Edmonds Commerce](https://www.edmondscommerce.co.uk)

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/00a50e56835f45b0ba32eed9c0285ede)](https://www.codacy.com/app/edmondscommerce/doctrine-static-meta?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=edmondscommerce/doctrine-static-meta&amp;utm_campaign=Badge_Grade) 
[![Build Status](https://travis-ci.org/edmondscommerce/doctrine-static-meta.svg?branch=master)](https://travis-ci.org/edmondscommerce/doctrine-static-meta)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/edmondscommerce/doctrine-static-meta/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/edmondscommerce/doctrine-static-meta/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/edmondscommerce/doctrine-static-meta/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/edmondscommerce/doctrine-static-meta/?branch=master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/edmondscommerce/doctrine-static-meta/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
[![Maintainability](https://api.codeclimate.com/v1/badges/fd4655978dc2137dd375/maintainability)](https://codeclimate.com/github/edmondscommerce/doctrine-static-meta/maintainability)

An implementation of Doctrine using the [PHP Static Meta Data driver](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/php-mapping.html#static-function) and no annotations.

This library includes extensive traits and interfaces and also full code generation allowing you to set up a project quickly.

## Install

```bash
composer require edmondscommerce/typesafe-functions dev-master@dev
composer require edmondscommerce/doctrine-static-meta dev-master@dev
```

## Limitations

Whilst this is now at a stage where we are using it in production, it is still a work in progress.

* Currently we have only targeted MySQL

## Suggested .my.cnf File

As DSM is using binary ID columns, it is worth customising your .my.cnf file to make this easier to work with

```
[mysql]
auto-rehash
binary-as-hex = true                                                                                              
[client]
user=root
password=YOURPASSWORDHERE
```

## Further Reading

Documention is still very much a work in progress...

Have a look in the [docs](docs) Folder
