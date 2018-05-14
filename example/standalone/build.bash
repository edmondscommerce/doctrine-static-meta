#!/usr/bin/env bash
# This is a BASH script that will build the entire example project
# You can use this as a bit of an illustration of the way you might initialise a project

#This is a standard script header that normalises the location and sets up sane error handling
readonly DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )";
cd $DIR;
set -e
set -u
set -o pipefail
#standardIFS="$IFS"
IFS=$'\n\t'

#A simple script header
echo "
===========================================
$(hostname) $0 $@
===========================================
"

#First, some sanity checking. This script is destructive!
echo "ABOUT TO TOTALLY DESTROY AND RECREATE THIS EXAMPLE.."
read -p "Are you sure???"  -n 1 -r
echo    # (optional) move to a new line
if [[ ! $REPLY =~ ^[Yy]$ ]]
then
    echo "aborting..."
    exit 1
fi

bash $DIR/build/step1.bash

bash $DIR/build/step2.bash

cd project

echo "# Doctrine Static Meta Example

This is an example project that utilises the Doctrine Static Meta library

You should refer to the main [README.md](https://www.github.com/edmondscommerce/doctrine-static-meta) for further information
" > README.md

bash bin/qa

echo "
===========================================
$(hostname) $0 $@ COMPLETED
===========================================
"
