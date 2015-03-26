#!/bin/bash

DIR="$(cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd)"

success() {
	echo " Success!"
}

failure() {
	echo " Failed!"
}

# Make sure we are in the right directory.
cd "$DIR"

# Get MySQL info.
echo -en "MySQL username (root): "; read NAME
NAME=${NAME:-root}
echo -en "MySQL password: "; read -s PASS
echo -en "\nMySQL server (localhost): "; read SERVER
SERVER=${SERVER:-localhost}
echo -en "MySQL db name (omgcatz): "; read DB_NAME
DB_NAME=${DB_NAME:-omgcatz}
echo -en "\n8tracks API key: "; read EIGHT_API_KEY

echo -en "\nAre you going to use slave servers? [y/n]: "; read HAS_SLAVES
if [ "$HAS_SLAVES" == "y" ]; then
	while :; do
		echo -en "Domain, IP, or blank to continue: "; read SERVER
		[ -z "$SERVER" ] && break
		SERVERS="$SERVERS\"$SERVER\","
	done

	SLAVES="public \$slaves = array($SERVERS);"
else
	if [ ! -d "../api/stuff/download" ]; then
		echo
		git clone "https://github.com/omgcatz/omgcatz-slave/" "../api/download"
		echo -e "\nRunning api/download/_install/setup.sh..."
		../api/stuff/download/_install/setup.sh
	fi
fi

# Create DatabaseCredentials class.
echo "<?php class Config { protected \$server=\"$SERVER\",\$user=\"$NAME\",\$password=\"$PASS\",\$database=\"$DB_NAME\",\$eightApiKey=\"$EIGHT_API_KEY\"; $SLAVES }" > ../api/include/Config.php

# Optionally create database.
echo -en "\nCreate database $DB_NAME? [y/n]: "; read ANSWER
if [ "$ANSWER" == "y" ]; then
	echo -en "Creating database..."
	echo "create database $DB_NAME" | mysql -u"$NAME" -h"$SERVER" -p"$PASS" &> /dev/null && success || failure
fi

# Optionally create tables.
echo -en "\nCreate tables in $DB_NAME? [y/n]: "; read ANSWER
if [ "$ANSWER" == "y" ]; then
	echo -en "Creating tables..."
	php ./create-tables.php && success || failure

	if [ "$HAS_SLAVES" == "y" ]; then
		echo -en "Adding slave servers..."
		php ./install-slaves.php && success || failure
	fi
fi
