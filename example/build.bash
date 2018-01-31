#!/usr/bin/env bash
readonly DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )";
cd $DIR;
set -e
set -u
set -o pipefail
standardIFS="$IFS"
IFS=$'\n\t'
function phpNoXdebug {
    debugMode="off"
    if [[ "$-" == *x* ]]
    then
        debugMode='on'
    fi
    if [[ "$debugMode" == "on" ]]
    then
        set +x
    fi
    local returnCode;
    local temporaryPath="$(mktemp -t php.XXXX).ini"
    # Using awk to ensure that files ending without newlines do not lead to configuration error
    /usr/bin/php -i | grep "\.ini" | grep -o -e '\(/[a-z0-9._-]\+\)\+\.ini' | grep -v xdebug | xargs awk 'FNR==1{print ""}1' > "$temporaryPath"
    #Run PHP with temp config with no xdebug, display errors on stderr
    set +e
    /usr/bin/php -n -c "$temporaryPath" "$@"
    returnCode=$?
    set -e
    rm -f "$temporaryPath"
    if [[ "$debugMode" == "on" ]]
    then
        set -x
    fi
    return $returnCode;
}
echo "
===========================================
$(hostname) $0 $@
===========================================
"
echo "ABOUT TO TOTALLY DESTROY AND RECREATE THIS EXAMPLE.."
read -p "Are you sure???"  -n 1 -r
echo    # (optional) move to a new line
if [[ ! $REPLY =~ ^[Yy]$ ]]
then
    echo "aborting..."
    exit 1
fi
cd project
rm -rf ./*

echo "Creating required directories"
mkdir -p src/Entities
mkdir -p tests

echo "Creating required cli-config.php file"
cat <<'PHP' > cli-config.php
<?php declare(strict_types=1);

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\DoctrineExtend;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\DevEntityManagerFactory;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;

require __DIR__ . '/vendor/autoload.php';

$entityManager = DevEntityManagerFactory::setupAndGetEm();

// This adds the DSM commands into the standard doctrine bin
$commands = DoctrineExtend::getCommands();

return ConsoleRunner::createHelperSet($entityManager);
PHP

echo "Creating PHPUnit XML config file"
cat <<'XML' > phpunit.xml
<phpunit
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/6.3/phpunit.xsd"
        backupGlobals="true"
        bootstrap="tests/bootstrap.php"
        cacheTokens="false"
        colors="true"
        verbose="true">
</phpunit>
XML

echo "Creating a .env file"
if [[ -f ~/.my.cnf ]]
then
    dbUser=$(grep user= ~/.my.cnf | cut -d '=' -f 2)
    dbPass=$(grep password= ~/.my.cnf | cut -d '=' -f 2)
else
    echo "please enter your db user:"
fi
dbHost=localhost
dbName=dsm_example
echo "
dbUser=$dbUser
dbPass=$dbPass
dbHost=$dbHost
dbName=$dbName
" > .env

echo "Creating Database"
mysql -e "CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci"

echo "Creating composer.json file"
cat <<'JSON' > composer.json
{
  "require": {
    "edmondscommerce/doctrine-static-meta": "dev-master"
  },
  "require-dev": {
    "phpunit/phpunit": "^6.3",
    "fzaninotto/faker": "^1.7"
  },
  "autoload": {
    "psr-4": {
      "My\\Test\\Project\\": [
        "src/"
      ]
    }
  },
  "autoload-dev": {
    "psr-4": {
      "My\\Test\\Project\\": [
        "tests/"
      ]
    }
  },
  "config": {
    "bin-dir": "bin",
    "preferred-install": "dist",
    "optimize-autoloader": true
  },
  "minimum-stability": "dev",
  "prefer-stable": true
}
JSON

echo "Running composer install"
phpNoXdebug $(which composer) install --prefer-dist

echo "Building Entities"
rootNs="My\Test\Project\Entities\\"
entitiesToBuild="
${rootNs}Address
${rootNs}Customer
${rootNs}Customer\Segment
${rootNs}Customer\Category
${rootNs}Order
${rootNs}Order\Address
${rootNs}Order\LineItem
${rootNs}Product
${rootNs}Product\Brand
"
for entity in $entitiesToBuild
do
    phpNoXdebug ./bin/doctrine dsm:generate:entity --entity-fully-qualified-name="$entity"
done

echo "Setting Relations Between Entities"


#full command with long options
phpNoXdebug ./bin/doctrine dsm:set:relation --entity1="${rootNs}Customer"       --hasType=ManyToMany              --entity2="${rootNs}Address"

#minimalist command with short options, note the shorthand syntax for specifying the command to run
phpNoXdebug ./bin/doctrine d:s:r -m "${rootNs}Customer"       -t ManyToMany              -i "${rootNs}Customer\Segment"

phpNoXdebug ./bin/doctrine d:s:r -m "${rootNs}Customer"       -t ManyToMany              -i "${rootNs}Customer\Category"

phpNoXdebug ./bin/doctrine d:s:r -m "${rootNs}Customer"       -t OneToMany               -i "${rootNs}Order"

phpNoXdebug ./bin/doctrine d:s:r -m "${rootNs}Order"          -t OneToMany               -i "${rootNs}Order\Address"
                                                                           
phpNoXdebug ./bin/doctrine d:s:r -m "${rootNs}Order\Address"  -t UnidirectionalOneToOne  -i "${rootNs}Address"
                                                                           
phpNoXdebug ./bin/doctrine d:s:r -m "${rootNs}Order"          -t OneToMany               -i "${rootNs}Order\LineItem"

phpNoXdebug ./bin/doctrine d:s:r -m "${rootNs}Order\LineItem" -t OneToOne                -i "${rootNs}Product"

phpNoXdebug ./bin/doctrine d:s:r -m "${rootNs}Product"  -t OneToOne                -i "${rootNs}Product\Brand"

echo "
===========================================
$(hostname) $0 $@ COMPLETED
===========================================
"
