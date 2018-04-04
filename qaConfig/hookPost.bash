#!/usr/bin/env bash

if [[ $TRAVIS != 'true' ]]
then
    if [[ "$(git branch | grep '* master')" != "" ]]
    then
        echo "

    Pushing Changes (required to build example)
    -------------------------------------------
    "
        git push
        sleep 2;

        echo "

    Rebuilding the example code
    ---------------------------
    "

        cd ${projectRoot}/example;

        bash build.bash;
    fi
fi