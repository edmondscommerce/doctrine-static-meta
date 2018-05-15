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

gitBranch=$(if [ "$TRAVIS_PULL_REQUEST" == "false" ]; then echo $TRAVIS_BRANCH; else echo $TRAVIS_PULL_REQUEST_BRANCH; fi)
export gitBranch
git checkout $gitBranch
rm -f composer.lock
composer install
git checkout HEAD composer.lock

dbUser=dsm
dbPass=dsm
dbName=dsm
dbHost=localhost

mysql -e "CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET utf8 COLLATE utf8_bin;"
mysql -e "GRANT ALL PRIVILEGES on *.* TO '$dbUser'@'$dbHost' IDENTIFIED BY '$dbPass' WITH GRANT OPTION;"
mysql -e "FLUSH PRIVILEGES;"
mysql -e "SHOW GRANTS FOR '$dbUser'@'$dbHost'"

cat << EOF > $DIR/.env
export dbUser="$dbUser"
export dbPass="$dbPass"
export dbHost="$dbHost"
export dbName="$dbName"
EOF

mkdir -p $DIR/cache/Proxies && chmod 777 $DIR/cache/Proxies
mkdir -p $DIR/cache/qa && chmod 777 $DIR/cache/qa

export phpUnitQuickTests=0
# By default we don't want travis to generate coverage
export phpUnitCoverage=0
if [[ ${GENERATE_COVERAGE} == "1" ]]
then
    # If we've asked travis to generate coverage then switch the variable to on
    export phpUnitCoverage=1
    # We need to remove the root phpunit.xml file otherwise the coverage one won't be used
    rm ${DIR}/phpunit.xml
fi

bin/qa



echo "
===========================================
$(hostname) $0 $@ COMPLETED
===========================================
"
