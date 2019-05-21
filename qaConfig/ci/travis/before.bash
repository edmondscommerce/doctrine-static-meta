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

echo "Creating PHP No Xdebug Config File:"
phpNoXdebugConfigFile="/tmp/php-noxdebug.ini"
php -i | grep "\.ini" | grep -o -e '\(/[a-z0-9._-]\+\)\+\.ini' | grep -v xdebug | xargs awk 'FNR==1{print ""}1' > "$phpNoXdebugConfigFile"
function phpNoXdebug(){
    php -n -c "$phpNoXdebugConfigFile" "$@"
}

gitBranch=$TRAVIS_BRANCH

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

echo "Running composer"
rm -f composer.lock
composerPath="$(which composer)"
phpNoXdebug ${composerPath} config github-oauth.github.com ${GITHUB_TOKEN}
git config github.accesstoken ${GITHUB_TOKEN}
phpNoXdebug ${composerPath} config --global github-protocols https
phpNoXdebug ${composerPath} global require hirak/prestissimo
phpNoXdebug ${composerPath} install
git checkout HEAD composer.lock
echo "Done"

echo "Moving SQL to ramdisk"
sudo mkdir /mnt/ramdisk
sudo mount -t tmpfs -o size=1024m tmpfs /mnt/ramdisk
sudo stop mysql
sudo mv /var/lib/mysql /mnt/ramdisk
sudo ln -s /mnt/ramdisk/mysql /var/lib/mysql
sudo start mysql
echo "Done"



echo "
===========================================
$(hostname) $0 $@ COMPLETED
===========================================
"
