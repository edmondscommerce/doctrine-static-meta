#!/usr/bin/env bash

echo "

Rebuilding the PHPstorm meta
----------------------------
"
php -f $projectRoot/.phpstorm.meta.php/build.php

echo "

Updating Composer
-----------------
"
composer update
