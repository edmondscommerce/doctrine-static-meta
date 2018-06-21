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
cp "${DIR}/../../../cli-config.php" ./

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
dbName=dsm_example_standalone_bash
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

dsmEntityNs="EdmondsCommerce\DoctrineStaticMeta\Entity\\"
rootEntitiesNs="My\Test\Project\Entities\\"

echo "

Working on Entities
-------------------
"


echo "
Building Entities
"
entitiesToBuild="
${rootEntitiesNs}Address
${rootEntitiesNs}Customer
${rootEntitiesNs}Customer\Segment
${rootEntitiesNs}Customer\Category
${rootEntitiesNs}Order
${rootEntitiesNs}Order\Address
${rootEntitiesNs}Order\LineItem
${rootEntitiesNs}Product
${rootEntitiesNs}Product\Brand
"
for entity in $entitiesToBuild
do
    phpNoXdebug ./bin/doctrine dsm:generate:entity --entity-fully-qualified-name="$entity"
done

echo "
Setting Relations Between Entities
"
#full command with long options
phpNoXdebug ./bin/doctrine dsm:set:relation \
    --entity1="${rootEntitiesNs}Customer" \
    --hasType=ManyToMany \
    --entity2="${rootEntitiesNs}Address"

#minimalist command with short options, note the shorthand syntax for specifying the command to run
phpNoXdebug ./bin/doctrine d:s:r -m "${rootEntitiesNs}Customer"       -t ManyToMany             -i "${rootEntitiesNs}Customer\Segment"

phpNoXdebug ./bin/doctrine d:s:r -m "${rootEntitiesNs}Customer"       -t ManyToMany             -i "${rootEntitiesNs}Customer\Category"

phpNoXdebug ./bin/doctrine d:s:r -m "${rootEntitiesNs}Customer"       -t OneToMany              -i "${rootEntitiesNs}Order"

phpNoXdebug ./bin/doctrine d:s:r -m "${rootEntitiesNs}Order"          -t OneToMany              -i "${rootEntitiesNs}Order\Address"

phpNoXdebug ./bin/doctrine d:s:r -m "${rootEntitiesNs}Order\Address"  -t UnidirectionalOneToOne -i "${rootEntitiesNs}Address"

phpNoXdebug ./bin/doctrine d:s:r -m "${rootEntitiesNs}Order"          -t OneToMany              -i "${rootEntitiesNs}Order\LineItem"

phpNoXdebug ./bin/doctrine d:s:r -m "${rootEntitiesNs}Order\LineItem" -t OneToOne               -i "${rootEntitiesNs}Product"

phpNoXdebug ./bin/doctrine d:s:r -m "${rootEntitiesNs}Product"        -t OneToOne               -i "${rootEntitiesNs}Product\Brand"
echo "

Working on Fields
-------------------
"
echo "
Creating Fields
"
rootFieldNs="My\Test\Project\Entity\Fields\Traits\\"

phpNoXdebug ./bin/doctrine d:g:f -f "${rootFieldNs}Product\SKUFieldTrait" -d string --not-nullable --is-unique

phpNoXdebug ./bin/doctrine d:g:f -f "${rootFieldNs}Product\NameFieldTrait" -d string --not-nullable

echo "
Creating Fields from Archetypes
"
phpNoXdebug ./bin/doctrine d:g:f -f "${rootFieldNs}\Order\OrderPlaced"    -d $dsmEntityNs\Fields\Traits\Date\TimestampFieldTrait  --not-nullable

echo "
Assigning Fields to Entities
"
phpNoXdebug ./bin/doctrine d:s:f --entity="${rootEntitiesNs}Product" --field="${rootFieldNs}Product\SKUFieldTrait"

phpNoXdebug ./bin/doctrine d:s:f --entity="${rootEntitiesNs}Product" --field="${rootFieldNs}Product\NameFieldTrait"
echo "

Working on Embeddables
-------------------
"


echo "
Creating Embeddables from Archetypes
"
phpNoXdebug ./bin/doctrine d:g:e --class='SalePriceEmbeddable' --archetype="${dsmEntityNs}Embeddable/Financial/MoneyEmbeddable"

phpNoXdebug ./bin/doctrine d:g:e --class='CostPriceEmbeddable' --archetype="${dsmEntityNs}Embeddable/Financial/MoneyEmbeddable"

phpNoXdebug ./bin/doctrine d:g:e --class='ShippingPriceEmbeddable' --archetype="${dsmEntityNs}Embeddable/Financial/MoneyEmbeddable"

phpNoXdebug ./bin/doctrine d:g:e --class='TotalPriceEmbeddable' --archetype="${dsmEntityNs}Embeddable/Financial/MoneyEmbeddable"






echo "
===========================================
$(hostname) $0 $@ COMPLETED
===========================================
"
