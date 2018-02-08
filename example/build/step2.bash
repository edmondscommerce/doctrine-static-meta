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

#This is a Bash function that allows you to run CLI PHP commands without Xdebug, it makes it a LOT faster
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
cd into the project and generate code
"
cd $DIR/../project

echo "
Creating required directories and emptying out anything existing
"
mkdir -p src
mkdir -p tests
mkdir -p cache
rm -rf src/Entities/*
rm -rf tests/*
rm -rf cache/*
mkdir -p src/Entities
mkdir -p cache/Proxies

echo "
Creating required cli-config.php file
"
cp "${DIR}/../../cli-config.php" ./

echo "
Creating PHPUnit XML config file
"
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

echo "
Creating a .env file
"
if [[ -f ~/.my.cnf ]]
then
    dbUser=$(grep user= ~/.my.cnf | cut -d '=' -f 2)
    dbPass=$(grep password= ~/.my.cnf | cut -d '=' -f 2)
else
    echo "please enter your db user:"
    read dbUser
    echo "please enter your db pass:"
    read dbPass
fi
dbHost=localhost
dbName=dsm_example
echo "
dbUser=$dbUser
dbPass=$dbPass
dbHost=$dbHost
dbName=$dbName
" > .env

echo "
Creating Database
"
mysql -e "DROP DATABASE IF EXISTS $dbName "
mysql -e "CREATE DATABASE $dbName CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci"



echo "
Building Entities
"
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

echo "
Setting Relations Between Entities
"
#full command with long options
phpNoXdebug ./bin/doctrine dsm:set:relation --entity1="${rootNs}Customer"       --hasType=ManyToMany              --entity2="${rootNs}Address"

#minimalist command with short options, note the shorthand syntax for specifying the command to run
phpNoXdebug ./bin/doctrine d:s:r -m "${rootNs}Customer"       -t ManyToMany                     -i "${rootNs}Customer\Segment"

phpNoXdebug ./bin/doctrine d:s:r -m "${rootNs}Customer"       -t ManyToMany                     -i "${rootNs}Customer\Category"

phpNoXdebug ./bin/doctrine d:s:r -m "${rootNs}Customer"       -t OneToMany                      -i "${rootNs}Order"

phpNoXdebug ./bin/doctrine d:s:r -m "${rootNs}Order"          -t OneToMany                      -i "${rootNs}Order\Address"

phpNoXdebug ./bin/doctrine d:s:r -m "${rootNs}Order\Address"  -t UnidirectionalOneToOne         -i "${rootNs}Address"

phpNoXdebug ./bin/doctrine d:s:r -m "${rootNs}Order"          -t OneToMany                      -i "${rootNs}Order\LineItem"

phpNoXdebug ./bin/doctrine d:s:r -m "${rootNs}Order\LineItem" -t OneToOne                       -i "${rootNs}Product"

phpNoXdebug ./bin/doctrine d:s:r -m "${rootNs}Product"        -t OneToOne                       -i "${rootNs}Product\Brand"

echo "
===========================================
$(hostname) $0 $@ COMPLETED
===========================================
"
