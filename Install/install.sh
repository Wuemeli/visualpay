//DO NOT USE IT NOW ITS IN PROGRESS
set -e;
echo "Installing Dependencies";
sudo apt update -y && sudo apt upgrade -y
sudo apt install composer -y
sudo apt-get install git-all -y
echo "Downloading Source Code";
mkdir -p "visualpay"
cd visualpay
git@github.com:Wuemeli/visualpay.git

echo "To complete installation of 'VisualPay' edit the Config PHP File with your Database Informations";
echo "Then use the pre-made MySQL Scheme that you find in the install Folder";

