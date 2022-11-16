//DO NOT USE IT NOW ITS IN PROGRESS
set -e;
echo "Installing Dependencies";
sudo apt update -y && sudo apt upgrade -y
sudo apt install composer -y
sudo apt-get install git-all -y
sudo apt-get install php -y
echo "Creating an Folder and Downloading Source Code";
mkdir -p "visualpay"
cd visualpay
//In Progress with Source Code Downloading
git@github.com:Wuemeli/visualpay.git

//Creating the Database and Importing the Scheme
#!/bin/bash
# If /root/.my.cnf exists then it won't ask for root password
if [ -f /root/.my.cnf ]; then
	echo "Please enter the NAME of the new MySQL database user! (example: user1)"
	read username
	echo "Please enter the PASSWORD for the new MySQL database user!"
	echo "Note: password will be hidden when typing"
	read -s userpass
	echo "Creating new user..."
	mysql -e "CREATE USER ${username}@localhost IDENTIFIED BY '${userpass}';"
	echo "User successfully created!"
	echo ""
	echo "Granting ALL privileges on visualpay to ${username}!"
	mysql -e "GRANT ALL PRIVILEGES ON visualpay.* TO '${username}'@'localhost';"
	mysql -e "FLUSH PRIVILEGES;"
	echo "Created the Database User and edited the config.php :)"
	exit
# If /root/.my.cnf doesn't exist then it'll ask for root password	
else
	echo "Please enter the NAME of the new MySQL database user! (example: user1)"
	read username
	echo "Please enter the PASSWORD for the new MySQL database user!"
	echo "Note: password will be hidden when typing"
	read -s userpass
	echo "Creating new user..."
	mysql -uroot -p${rootpasswd} -e "CREATE USER ${username}@localhost IDENTIFIED BY '${userpass}';"
	echo "User successfully created!"
	echo ""
	echo "Granting ALL privileges on visualpay to ${username}!"
	mysql -uroot -p${rootpasswd} -e "GRANT ALL PRIVILEGES ON visualpay.* TO '${username}'@'localhost';"
	mysql -uroot -p${rootpasswd} -e "FLUSH PRIVILEGES;"
	echo "Created the Database User and edited the config.php :)"
	exit
fi

echo "To complete installation of 'VisualPay' edit the Config PHP File with your Database Information";
echo "Then use the pre-made MySQL Scheme that you find in the install Folder";
