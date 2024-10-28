mkdir ~/.ssh
chmod 700 ~/.ssh
touch ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
ssh -i "~/.ssh/id_rsa" mijung@ec2-3-86-228-53.compute-1.amazonaws.com
exit
mkdir -p ~/.ssh
chmod 700 ~/.ssh
touch ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
nano ~/.ssh/authorized_keys
exit
sudo yum groupinstall "Development Tools"
sudo yum install kernel-devel kernel-headers
date
sudo timedatectl set-timezone America/Chicago
date
sudo cp /usr/share/zoneinfo/America/Chicago /etc/localtime
date
sudo yum update -y
sudo yum install httpd -y
sudo systemctl start httpd
sudo systemctl enable httpd
sudo systemctl status httpd
sudo nano /etc/httpd/conf.d/userdir.conf
mkdir ~/public_html
sudo chmod o+x /home/mijung
sudo chmod o+rx /home/mijung/public_html
sudo chmod o+x /home/mijung
sudo chmod o+rx /home/mijung/public_html
ls
cd public_html
sudo chmod o+rx /home/mijung/public_html
echo 'APACHE WORKING!<3' > ~/public_html/hello.txt
sudo systemctl restart http
sudo systemctl enable httpd
sudo systemctl status httpd
sudo systemctl restart httpd
sudo systemctl status httpd
sudo firewall-cmd --list-all
sudo firewall-cmd --add-service=http --permanent
sudo firewall-cmd --reload
sudo vi /etc/httpd/conf.d/userdir.conf
sudo tail -f /var/log/httpd/error_log
101exit
exit
sudo systemctl restart httpd
sudo /usr/sbin/apachectl restart
sudo apachectl restart
sudo /etc/init.d/httpd restart
sudo systemctl restart httpd
sudo systemctl status httpd
chmod o+x ~
chmod o+rx ~/public_html
UserDir public_html
sudo systemctl restart httpd
sudo tail -f /var/log/httpd/error_log
ls
mkdir .ssh
sudo ls
ls
sudo mkdir .ssh
cd
ls
mkdir .ssh
chmod 700 ,ssh
chmod 700 .ssh
touch .ssh/authorized_keys
chmod 600 .ssh/authorized_keys
nano .ssh/authorized_keys
ls -l /home/mijung/public_html
chmod u+w /path/to/your/files/your_file.txt
chmod u+w /home/mijung/
ls -l /home/mijung/public_html
chmod u+w /home/mijung/public_html
ls -l /home/mijung/public_html
ls /home/mijung
ls
cd public_html/
ls
ls
cd public_html/
ls
mkdir phpCalculator.php
ls
rm phpCalculator.php/
vi phpCalculator.php
