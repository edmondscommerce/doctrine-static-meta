#!/usr/bin/env bash

if [[ -z "${TRAVIS+x}" ]] && [[ "$(git branch | grep '* master')" == "" ]] && [[ "${phpqaQuickTests:-''}" == "0" ]]
then
        echo "
====================================================

ALL TESTS PASS - REBUILDING THE EXAMPLE...

====================================================

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
