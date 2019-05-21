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
echo "Setting up DB"
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

dbName=dsm_test

mysql -e "CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET utf8 COLLATE utf8_bin;"
mysql -e "GRANT ALL PRIVILEGES on *.* TO '$dbUser'@'$dbHost' IDENTIFIED BY '$dbPass' WITH GRANT OPTION;"
mysql -e "FLUSH PRIVILEGES;"
mysql -e "SHOW GRANTS FOR '$dbUser'@'$dbHost'"
echo "Done"

echo "Creating directories:"
sudo mkdir -p $DIR/cache/
sudo mount -t tmpfs -o size=128m tmpfs $DIR/cache/
mkdir -p $DIR/cache/Proxies && chmod 777 $DIR/cache/Proxies
mkdir -p $DIR/cache/qa && chmod 777 $DIR/cache/qa

sudo mkdir -p $DIR/var/
sudo mount -t tmpfs -o size=128m tmpfs $DIR/var/
echo "Done"

echo "Running QA Pipeline"
export phpUnitQuickTests=${phpUnitQuickTests:-0}
export phpUnitCoverage=${phpUnitCoverage:-0}
export phpUnitTestsSubDir=${phpUnitTestsSubDir:-''}

if [[ "$TRAVIS_COMMIT_MESSAGE" == *xdebug* ]]
then
    echo "commit message contains xdebug which causes problems, fixing"
    export TRAVIS_COMMIT_MESSAGE="${TRAVIS_COMMIT_MESSAGE/xdebug/xd3bug/}"
    echo "Done"
fi

#Run the QA command, defaults to bin/qa
bash -c "${qaCmd:-bin/qa}"
echo "Done"

echo "Unmounting tmpfs directories"
sudo umount $DIR/cache/
sudo umount $DIR/var/
echo "Done"

echo "
===========================================
$(hostname) $0 $@ COMPLETED
===========================================
"
