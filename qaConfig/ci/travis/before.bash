#!/usr/bin/env bash
readonly DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )";
cd $DIR;
set -e
set -u
set -o pipefail
#standardIFS="$IFS"
IFS=$'\n\t'
echo "
===========================================
$(hostname) $0 $@
===========================================
"
cd $TRAVIS_BUILD_DIR

if [[ ${phpUnitCoverage} == "0" ]];
then
    phpenv config-rm xdebug.ini;
fi
composer config github-oauth.github.com ${GITHUB_TOKEN}
git config github.accesstoken ${GITHUB_TOKEN}
composer config --global github-protocols https


gitBranch=$TRAVIS_BRANCH
#if [[ ${phpUnitCoverage} == "1" && ( "${gitBranch}" != "master" || "false" != "$TRAVIS_PULL_REQUEST" ) ]]
#then
#    echo "
############################################################
#
#    ABORTING COVERAGE BUILD
#
#    Now only generating coverage in the master branch after pull requests
#
############################################################
#
#    "
#    exit 1
#fi

export qaCmd=bin/qa

# We use Travis Matrix to split our coverage build by specifying a phpUnitTestsSubDir
# Currently this is using a custom override of the PHPUnit tool in qaConfig/tools/phpunit.inc.bash
if [[ "" != "${phpUnitTestsSubDir}:-''" ]]
then
   export qaCmd="bin/qa -t unit"
fi

export gitBranch
git checkout $gitBranch

if [[ "false" != "$TRAVIS_PULL_REQUEST" ]]
then
    echo "
This is a pull request.
Merging the PR branch ($TRAVIS_PULL_REQUEST_BRANCH) into $gitBranch so we can test the outcome of a merge
"
    git merge origin/$TRAVIS_PULL_REQUEST_BRANCH
fi

rm -f composer.lock
composer install
git checkout HEAD composer.lock



echo "
===========================================
$(hostname) $0 $@ COMPLETED
===========================================
"
