#!/usr/bin/env bash

echo "

Rebuilding the PHPstorm meta
----------------------------
"
phpNoXdebug -f $projectRoot/.phpstorm.meta.php/build.php
set +x

echo "

Updating Composer
-----------------
"
phpNoXdebug -f $(which composer) update
git add composer.lock
if [[ "$(git status --porcelain)" == " M composer.lock" ]]
then
    echo "Commiting composer lock update"
    git add $projectRoot/composer.lock
    git commit -m "composer updated"
fi
set +x

