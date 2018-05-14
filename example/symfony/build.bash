#!/usr/bin/env bash

SOURCE="${BASH_SOURCE[0]}"
while [ -h "$SOURCE" ]; do # resolve $SOURCE until the file is no longer a symlink
  DIR="$( cd -P "$( dirname "$SOURCE" )" && pwd )"
  SOURCE="$(readlink "$SOURCE")"
  [[ $SOURCE != /* ]] && SOURCE="$DIR/$SOURCE" # if $SOURCE was a relative symlink, we need to resolve it relative to the path where the symlink file was located
done

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
# Error Handling
backTraceExit () {
    local err=$?
    set +o xtrace
    local code="${1:-1}"
    printf "\n\nError in ${BASH_SOURCE[1]}:${BASH_LINENO[0]}. '${BASH_COMMAND}'\n\n exited with status: \n\n$err\n\n"
    # Print out the stack trace described by $function_stack
    if [ ${#FUNCNAME[@]} -gt 2 ]
    then
        echo "Call tree:"
        for ((i=1;i<${#FUNCNAME[@]}-1;i++))
        do
            echo " $i: ${BASH_SOURCE[$i+1]}:${BASH_LINENO[$i]} ${FUNCNAME[$i]}(...)"
        done
    fi
    echo "Exiting with status ${code}"
    exit "${code}"
}
trap 'backTraceExit' ERR
set -o errtrace
# Error Handling Ends

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
    local temporaryPath="$(mktemp -t php.XXXX.ini)"
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

function generateEntity
{
    if (( "$#" != 1 ))
    then
        echo "generateEntity must be called with a 'FQN' argument";
        exit 1;
    fi

    local fqn=$1;

    phpNoXdebug ./bin/doctrine dsm:generate:entity --entity-fully-qualified-name="${fqn}"

}

function generateUuidEntity
{
    if (( "$#" != 1 ))
    then
        echo "generateUuidEntity must be called with a 'FQN' argument";
        exit 1;
    fi

    local fqn=$1;

    phpNoXdebug ./bin/doctrine dsm:generate:entity --entity-fully-qualified-name="${fqn}" --uuid-primary-key

}

function generateField
{
    if (( "$#" != 2 ))
    then
        echo "generateField must be called with 'name' and 'type' arguments";
        exit 1;
    fi

    local name=$1;
    local type=$2;

    phpNoXdebug ./bin/doctrine dsm:generate:field --field-fully-qualified-name="${name}" --field-property-doctrine-type="${type}";
}

function setField
{
    if (( "$#" != 2 ))
    then
        echo "setField must be called with 'entity FQN' and 'field FQN' arguments";
        exit 1;
    fi

    local entity=$1;
    local field=$2;

    phpNoXdebug ./bin/doctrine dsm:set:field --entity="${entity}" --field="${field}";
}

function setRelation
{
    if (( "$#" != 3 ))
    then
        echo "setRelation must be called with 'entity1 FQN', 'type' and 'entity2 FQN' arguments";
        exit 1;
    fi

    local e1=$1;
    local type=$2;
    local e2=$3;

    phpNoXdebug ./bin/doctrine dsm:set:relation --entity1="${e1}" --hasType="${type}" --entity2="${e2}";
}

cd $DIR/project

rm -rf src/Entities/*
rm -rf src/Entity/*
rm -rf tests/Entities/*

set +e
composer update
echo "

Symfony  4 / Composer issues... need looking at, but out of scope for this project

"
set -e

rootNs="My\Test\Project\Entities\\"

echo "
    Generating entities
";

entitiesToBuild=(
    "${rootNs}Client"
);

for entity in ${entitiesToBuild[*]}
do
    generateEntity "${entity}";
done

echo "
    Generating fields
";

fieldNs="My\Test\Project\Entity\Fields\Traits\\";

generateField "${fieldNs}name" 'string';

echo "
    Setting fields
";

setField "${rootNs}Client" "${fieldNs}NameFieldTrait";

echo "
    Done
";

./bin/doctrine orm:schema-tool:update;
