#!/usr/bin/env bash
readonly DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )";
cd $DIR;
set -e
set -u
set -o pipefail
standardIFS="$IFS"
IFS=$'\n\t'
echo "
===========================================
$(hostname) $0 $@
===========================================
"

if [[ -z ${rootPass:-} ]]
then
    echo "please enter your root mysql password:"
    read rootPass;
fi

export dbUser="root"
export dbPass="$rootPass"
export dbHost="localhost"
export dbName="doctrine_static_example"
export dbEntitiesPath="$DIR/ExampleEntities"

echo "Creating DB"
mysql -u $dbUser -p$dbPass -h $dbHost -e "DROP DATABASE IF EXISTS $dbName"
mysql -u $dbUser -p$dbPass -h $dbHost -e "CREATE DATABASE $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

mkdir -p $dbEntitiesPath
rm -rf $dbEntitiesPath/*

cd $DIR/../

bin/doctrine dsm:generate:entity \
    -p"$(pwd)/example" \
    -r"EdmondsCommerce\\DoctrineStaticMeta\\Example" \
    -f"EdmondsCommerce\\DoctrineStaticMeta\\Example\\ExampleEntities\\Person"

bin/doctrine dsm:generate:entity \
    -p"$(pwd)/example" \
    -r"EdmondsCommerce\\DoctrineStaticMeta\\Example" \
    -f"EdmondsCommerce\\DoctrineStaticMeta\\Example\\ExampleEntities\\Properties\\Address"

bin/doctrine dsm:generate:entity \
    -p"$(pwd)/example" \
    -r"EdmondsCommerce\\DoctrineStaticMeta\\Example" \
    -f"EdmondsCommerce\\DoctrineStaticMeta\\Example\\ExampleEntities\\Properties\\PhoneNumber"

bin/doctrine dsm:generate:relations \
    -p"$(pwd)/example" \
    -r"EdmondsCommerce\\DoctrineStaticMeta\\Example"

bin/doctrine orm:schema-tool:create
