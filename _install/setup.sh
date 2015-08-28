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
echo -en "\nMySQL server (localhost): "; read SQL_SERVER
SQL_SERVER=${SQL_SERVER:-localhost}
echo -en "MySQL db name (omgcatz): "; read DB_NAME
DB_NAME=${DB_NAME:-omgcatz}
echo -en "\n8tracks API key: "; read EIGHT_API_KEY

echo -en "\nAre you going to use minion servers? [y/n]: "; read HAS_minionS
if [ "$HAS_minionS" == "y" ]; then
	while :; do
		echo -en "minion root (e.g. http://s1.catz.io/): "; read SERVER
		[ -z "$SERVER" ] && break
		SERVERS="$SERVERS\"$SERVER\","
	done

	minionS="public static \$minions = array($SERVERS);"
else
	if [ ! -d "../api/stuff/download" ]; then
		echo
		git clone "https://github.com/cadejscroggins/omgcatz-minion/" "../api/stuff/download"
		echo -e "\nRunning api/download/_install/setup.sh..."
		../api/stuff/download/_install/setup.sh
	fi
fi

# Create DatabaseCredentials class.
echo "<?php class Config { public static \$server=\"$SQL_SERVER\",\$user=\"$NAME\",\$password=\"$PASS\",\$database=\"$DB_NAME\",\$eightTracksApiKey=\"$EIGHT_API_KEY\";$minionS }" > ../api/include/Config.php

# Optionally create database.
echo -en "\nCreate database $DB_NAME? [y/n]: "; read ANSWER
if [ "$ANSWER" == "y" ]; then
	echo -en "Creating database..."
	echo "create database $DB_NAME" | mysql -u"$NAME" -h"$SQL_SERVER" -p"$PASS" &> /dev/null && success || failure
fi

# Optionally create tables.
echo -en "\nCreate tables in $DB_NAME? [y/n]: "; read ANSWER
if [ "$ANSWER" == "y" ]; then
	echo -en "Creating tables..."
	php ./create-tables.php && success || failure

	if [ "$HAS_minionS" == "y" ]; then
		echo -en "Adding minion servers..."
		php ./install-minions.php && success || failure
	fi
fi
