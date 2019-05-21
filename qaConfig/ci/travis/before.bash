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
composer --version
composer config github-oauth.github.com ${GITHUB_TOKEN}
git config github.accesstoken ${GITHUB_TOKEN}
composer config --global github-protocols https
composer global require hirak/prestissimo
composer install
git checkout HEAD composer.lock
echo "Done"

echo "Moving SQL to ramdisk"
sudo mkdir /mnt/ramdisk
sudo mount -t tmpfs -o size=1024m tmpfs /mnt/ramdisk
sudo stop mysql
sudo mv /var/lib/mysql /mnt/ramdisk
sudo ln -s /mnt/ramdisk/mysql /var/lib/mysql
sudo cat <<'EOF' > /etc/mysql/my.cnf
[mysqld]
skip-log-bin
skip-external-locking
max_allowed_packet = 64M
table_open_cache = 4096
innodb_log_file_size = 10M
innodb_flush_log_at_trx_commit = 2
innodb_lock_wait_timeout = 180
EOF
sudo start mysql
echo "Done"



echo "
===========================================
$(hostname) $0 $@ COMPLETED
===========================================
"
