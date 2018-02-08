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
cd into the project and remove everything
"
cd $DIR/../project

rm -rf ./*

echo "
Creating composer.json file
"
cat <<'JSON' > composer.json
{
  "require": {
    "edmondscommerce/doctrine-static-meta": "~1",
    "php": ">=7.1"
  },
  "require-dev": {
    "phpunit/phpunit": "^6.3",cd ..
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

echo "
Running composer install
"
phpNoXdebug $(which composer) install --prefer-dist

echo "
===========================================
$(hostname) $0 $@ COMPLETED
===========================================
"
