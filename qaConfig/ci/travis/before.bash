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
cd $TRAVIS_BUILD_DIR
gitBranch=$(if [ "$TRAVIS_PULL_REQUEST" == "false" ]; then echo $TRAVIS_BRANCH; else echo $TRAVIS_PULL_REQUEST_BRANCH; fi)
export gitBranch
git checkout $gitBranch
rm -f composer.lock
composer install
git checkout HEAD composer.lock

if [[ ${phpUnitCoverage} == "0" ]];
then
    phpenv config-rm xdebug.ini;
fi
composer config github-oauth.github.com ${GITHUB_TOKEN}
git config github.accesstoken ${GITHUB_TOKEN}
composer config --global github-protocols https

echo "
===========================================
$(hostname) $0 $@ COMPLETED
===========================================
"
