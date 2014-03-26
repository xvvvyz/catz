#!/bin/bash

pform="%-32s"
bad=0

printf "$pform" "Creating folders..."
mkdir -p ./run/{archives,artwork,songs} && echo "Success!" || echo "Failed."

printf "$pform" "Changing folder permissions..."
chmod 777 ./run/{archives,artwork,songs} && echo "Success!" || echo "Failed."

printf "$pform" "Changing file permissions..."
chmod +x ./run/*/{*php,*sh} && echo "Success!" || echo "Failed."

printf "$pform" "Checking for eyeD3..."
if [ $(which eyeD3) ]; then
	echo "Success!"
	ln -fs `which eyeD3` ./run/download/eyeD3
else
	echo "Nada, please install."
	let bad+=1
fi

printf "$pform" "Checking for AtomicParsley..."
if [ $(which AtomicParsley) ]; then
	echo "Success!"
	ln -fs `which AtomicParsley` ./run/download/AtomicParsley
else
	echo "Nada, please install."
	let bad+=1
fi

printf "$pform" "Checking for p7zip..."
if [ $(which zip) ]; then
	echo "Success!"
	ln -fs `which zip` ./run/archives/zip
else
	echo "Nada, please install."
	let bad+=1
fi

if [ $bad -ne 0 ]; then
	printf "\nPlease install the missing commands ($bad) and run this script again.\n\n"
	exit 1
fi

printf "\n$pform" "MySQL username: "
read NAME
printf "$pform" "MySQL password: "
read -s PASS
printf "\n$pform" "MySQL server (localhost): "
read SERVER
printf "$pform" "MySQL database name: "
read DB_NAME

printf "\n$pform" "Creating ./run/database.php..."
echo "<?php \$con = mysqli_connect(\"$SERVER\", \"$NAME\", \"$PASS\", \"$DB_NAME\");" > ./run/include/database.php
chmod +x ./run/include/database.php
echo "Success!"

printf "\nPlease create a MySQL database called \"$DB_NAME\".\n"
printf "Move these files to wherever you set up your server.\n\nEnjoy!\n\n"
