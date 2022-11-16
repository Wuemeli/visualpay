//DO NOT USE IT NOW ITS IN PROGRESS
set -e;
echo "Installing Dependencies";
apt install composer -y
echo "Downloading Source Code";
mkdir -p "visualpay"
curl https://rawgit.com/oresoftware/quicklock/master/install.sh > "$HOME/.quicklock/ql.sh"
cd visualpay

echo "To complete installation of 'VisualPay' edit the Config PHP File with your Database Informations";
echo "Then use the pre-made MySQL Scheme that you find in the install Folder";

