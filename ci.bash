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

export phpUnitQuickTests=${phpUnitQuickTests:-0}
export phpUnitCoverage=${phpUnitCoverage:-0}

#Run the QA command, defaults to bin/qa
$qaCmd



echo "
===========================================
$(hostname) $0 $@ COMPLETED
===========================================
"
