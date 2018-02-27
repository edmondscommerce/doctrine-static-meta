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
set +x

if [[ "$phpUnitQuickTests" == "0" ]];
then

    echo "

Checking for uncommitted changes on full tests
----------------------------------------------
"
    checkForUncommittedChanges
fi