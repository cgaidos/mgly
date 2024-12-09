echo -e "\n[mysqld]\nlocal-infile=1\n\n[mysql]\nlocal-infile=1" | sudo tee --append /etc/mysql/my.cnf
sudo apt-get install php5-mysqlnd
sudo service mysql restart