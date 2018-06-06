#!/usr/bin/env bash

# Don't do this stuff if we are in single tool mode
if [[ "" == "$singleToolToRun" ]]
then

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
    if [[ "$(git status --porcelain)" == "M  composer.lock" ]]
    then
        echo "Committing composer lock update"
        git add $projectRoot/composer.lock
        git commit -m "composer updated"
    fi
    set +x
else
    echo "skipping pre hook processes for single tool run"
fi
