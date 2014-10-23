#!/bin/bash

DIR="$(cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd)"
INDENT="%-34s"
BAD=0

success() {
	echo "Success!"
}

failure() {
	echo "Failed!"
}

please() {
	echo "Nada, please install."
}

checkCommand() {
	printf "$INDENT" "Checking for $1..."
	if [ $(which $1) ]; then
		return 0
	else
		let BAD+=1
		return 1
	fi
}

# Make sure we are in the right directory.
cd "$DIR"

# Check for commands.
checkCommand "curl" && success || please
checkCommand "eyeD3" && success && ln -fs `which eyeD3` ../api/download/eyeD3 || please
checkCommand "AtomicParsley" && success && ln -fs `which AtomicParsley` ../api/download/AtomicParsley || please
checkCommand "zip" && success && ln -fs `which zip` ../api/download/archives/zip || please
checkCommand "find" && success && ln -fs `which find` ../api/download/archives/find || please
checkCommand "file" && success || please
checkCommand "php" && success || please

# If they need to install stuff let them know.
if [ $BAD -ne 0 ]; then
	printf "\nPlease install the missing commands ($BAD) and run this script again.\n\n"
	exit 1
fi

# Create folders.
printf "$INDENT" "Creating folders..."
mkdir -p ../api/download/{archives,artwork,songs} && echo "Success!" || echo "Failed."

# Update folder permissions.
printf "$INDENT" "Changing folder permissions..."
chmod 777 ../api/download/{archives,artwork,songs} && echo "Success!" || echo "Failed."

# Update file permissions.
printf "$INDENT" "Changing file permissions..."
chmod +x ../api/*/{*php,*sh} ./*{*php,*sh} && echo "Success!" || echo "Failed."

# Get MySQL info.
printf "\n$INDENT" "MySQL username: "; read NAME
printf "$INDENT" "MySQL password: "; read -s PASS
printf "\n$INDENT" "MySQL server (localhost): "; read SERVER
printf "$INDENT" "MySQL database name (omgcatz): "; read DB_NAME

# Create DatabaseCredentials class.
printf "\n$INDENT" "Creating DatabaseLogin class..."
echo "<?php class DatabaseLogin {protected \$server = \"$SERVER\";protected \$user = \"$NAME\";protected \$password = \"$PASS\";protected \$database = \"$DB_NAME\";}" > ../api/include/DatabaseLogin.php
echo "Success!"

# Optionally create database.
printf "\n$INDENT" "Create database $DB_NAME? [y/n]: "; read ANSWER
if [ "$ANSWER" == "y" ]; then
	printf "$INDENT" "Creating database..."
	echo "create database $DB_NAME" | mysql -u"$NAME" -h"$SERVER" -p"$PASS" &> /dev/null && success || failure
fi

# Optionally create tables.
printf "\n$INDENT" "Create tables in $DB_NAME? [y/n]: "; read ANSWER
if [ "$ANSWER" == "y" ]; then
	printf "$INDENT" "Creating tables..."
	php ./create-tables.php && success || failure
fi
