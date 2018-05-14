#!/usr/bin/env bash

if [[ -z "${TRAVIS+x}" ]] && [[ "$(git branch | grep '* master')" == "" ]] && [[ "${phpqaQuickTests:-''}" == "0" ]]
then
        echo "
====================================================

ALL TESTS PASS - YOU SHOULD REBUILD THE EXAMPLE AFTER YOU UPDATE MASTER

====================================================
"
fi
