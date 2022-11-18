set -e;
echo "Installing Dependencies";
sudo apt update -y && sudo apt upgrade -y
sudo apt install composer -y
sudo apt-get install git-all -y
sudo apt-get install php -y
composer install

echo "Creating an Folder and Downloading Source Code";
mkdir -p "visualpay"
cd visualpay
git@github.com:Wuemeli/visualpay.git

echo "Asking the Questions for the Installation"

echo "Fill the IP and Port Informations"

echo "Please Enter the IP where the Project should be hosted on?"
read ip
echo "Please enter the Port where the Project is pointed to?"
read port

echo "Admin Account Informations"

echo "Whats the Email for the Admin Account?"
read admail
echo "Whats the Username for the Admin Account?"
read aduser
echo "Whats the Password for the Admin Account? (Note please change the Password after Login because it is not encrypted)"
read -s adpass

echo "Fill now the Database Informations"

#!/bin/bash

echo "/root/.my.cnf exists dont asking for root Password"

if [ -f /root/.my.cnf ]; then
	echo "Please enter the NAME of the new MySQL database! (example: visualpay)"
	read dbname
	echo "Please enter the MySQL database CHARACTER SET! (example: latin1, utf8, ...)"
	echo "Enter utf8 if you don't know what you are doing"
	read charset
	echo "Creating new MySQL database..."
	mysql -e "CREATE DATABASE ${dbname} /*\!40100 DEFAULT CHARACTER SET ${charset} */;"
	echo "Database successfully created!"
	echo "Showing existing databases..."
	mysql -e "show databases;"
	echo ""
	echo "Please enter the NAME of the new MySQL database user! (example: user1)"
	read username
	echo "Please enter the PASSWORD for the new MySQL database user!"
	echo "Note: password will be hidden when typing"
	read -s userpass
	echo "Creating new user..."
	mysql -e "CREATE USER ${username}@localhost IDENTIFIED BY '${userpass}';"
	echo "User successfully created!"
	echo ""
	echo "Granting ALL privileges on ${dbname} to ${username}!"
	mysql -e "GRANT ALL PRIVILEGES ON ${dbname}.* TO '${username}'@'localhost';"
	mysql -e "FLUSH PRIVILEGES;"
	echo "You're good now :)"
	exit
	
echo "/root/.my.cnf doesn't exist asking for root password"

else
	echo "Please enter root user MySQL password!"
	echo "Note: password will be hidden when typing"
	read -s rootpasswd
	echo "Please enter the NAME of the new MySQL database! (example: visualpay)"
	read dbname
	echo "Please enter the MySQL database CHARACTER SET! (example: latin1, utf8, ...)"
	echo "Enter utf8 if you don't know what you are doing"
	read charset
	echo "Creating new MySQL database..."
	mysql -uroot -p${rootpasswd} -e "CREATE DATABASE ${dbname} /*\!40100 DEFAULT CHARACTER SET ${charset} */;"
	echo "Database successfully created!"
	echo "Showing existing databases..."
	mysql -uroot -p${rootpasswd} -e "show databases;"
	echo ""
	echo "Please enter the NAME of the new MySQL database user! (example: user1)"
	read username
	echo "Please enter the PASSWORD for the new MySQL database user!"
	echo "Note: password will be hidden when typing"
	read -s userpass
	echo "Creating new user..."
	mysql -uroot -p${rootpasswd} -e "CREATE USER ${username}@localhost IDENTIFIED BY '${userpass}';"
	echo "User successfully created!"
	echo ""
	echo "Granting ALL privileges on ${dbname} to ${username}!"
	mysql -uroot -p${rootpasswd} -e "GRANT ALL PRIVILEGES ON ${dbname}.* TO '${username}'@'localhost';"
	mysql -uroot -p${rootpasswd} -e "FLUSH PRIVILEGES;"
	echo "You're good now :)"
	exit
fi

echo "Importing the MySQL Scheme"

mysql -u ${username} -p ${dbname} < Install/visualpay.sql

echo "Creating the Config.PHP File and insert the Database Values"

cat > config.php << EOF
<?php
/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
define('DB_SERVER', 'localhost');
define('DB_USERNAME', '${username}');
define('DB_PASSWORD', '${userpass}');
define('DB_NAME', '${dbname}');
 
/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>
EOF

echo "Filling Admin-Data in an MySQL Database. (Currently this dont Works)"

$sql = "INSERT INTO user (id, username, password, email, rang, balance, status, email_verification_link, email_verified_at, verified) VALUES ('1', '$aduser', '$adpass', '$admail', '1', '0', '1', '0', '0', '1')";

echo "Starts the Application with php"
php -S ${ip}:${port}

echo "Now you can login With the Credentials you made at ${ip}:${port}";
echo "For more Informations (How to create an other Admin Account? Making GiftCard and co visit: https://github.com/visualpay/wiki";
echo "After Server-Reboot you need to run again php -S ${ip}:${port} to start the Application. More Informations for Startup see here: https://github.com/wuemeli/visualpay";
echo "If you want to start the Application in Background use: nohup php -S ${ip}:${port} &";
