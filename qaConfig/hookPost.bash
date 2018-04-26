#!/usr/bin/env bash

if [[ -z "${TRAVIS+x}" ]]
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

        cd ${projectRoot}/example/build_script;

        bash build.bash;
    fi
fi