# Setup Server for Laravel Application

## Setup DigitalOcean Server

- **Setup Ubuntu 24.04 LTS**: Ensure compatibility with required software versions.
- **Minimum 1GB RAM**: Adequate resources for smooth operation.
- **Use SSH Key**: Secure server access.

**Connect to new server with SSH**:

```bash
ssh root@ip
```

**Update and upgrade**:

```bash
apt update
apt upgrade
```

**Reboot**:

```bash
reboot
```

## Create a New Sudo User

```bash
adduser genranks # provide random password
usermod -aG sudo genranks
```

## Setup Firewall

```bash
ufw allow OpenSSH
ufw enable
```

## Add Public SSH Key to New User

```bash
su - genranks
mkdir .ssh
cd .ssh
nano authorized_keys # add public SSH key
exit
ssh -i .ssh/id_ed25519_genranks genranks@ip
```

## Installing Nginx Web Server

```bash
sudo apt install nginx
sudo ufw allow 'Nginx HTTP'
```

## Installing MySQL

```bash
sudo apt install mysql-server
sudo mysql_secure_installation
```

## Installing PHP

```bash
sudo apt install php8.3-fpm php-mysql php-mbstring php-xml php-bcmath php-curl
```

## Creating a Database for the Application

```bash
sudo mysql
CREATE DATABASE genranks;
CREATE USER 'genranks'@'localhost' IDENTIFIED WITH mysql_native_password BY 'password';
GRANT ALL ON genranks.* TO 'genranks'@'localhost';
FLUSH PRIVILEGES;
exit;
```

## Setting Up Nginx

```bash
sudo nano /etc/nginx/sites-available/genranks
```

```code
server {
    listen 80;
    server_name genranks.com www.genranks.com;
    root /var/www/genranks/current/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.html index.htm index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/genranks /etc/nginx/sites-enabled/
sudo unlink /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl reload nginx
```

## Adding User to www-data and Removing Password

```bash
sudo usermod -a -G www-data genranks
groups genranks
sudo passwd -d genranks
```

## Installing Certbot

```bash
sudo snap install core; sudo snap refresh core
sudo snap install --classic certbot
sudo ln -s /snap/bin/certbot /usr/bin/certbot
sudo systemctl reload nginx
```

## Allowing HTTPS Through the Firewall

```bash
sudo ufw allow 'Nginx Full'
sudo ufw delete allow 'Nginx HTTP'
```

## Obtaining an SSL Certificate

```bash
sudo certbot --nginx -d genranks.com -d www.genranks.com
```

## Verifying Certbot Auto-Renewal

```bash
sudo systemctl status snap.certbot.renew.service
sudo certbot renew --dry-run
```

## Ready Folders for Github Action

```bash
sudo mkdir /var/www/genranks
sudo rm -rf /var/www/html
sudo chmod -R 774 /var/www
sudo chown -R www-data:www-data /var/www
```

## Supervisor Configuration

```bash
sudo apt-get install supervisor
```

**Create a worker configuration file**:

```bash
sudo nano /etc/supervisor/conf.d/laravel-worker-default.conf
```

```code
[program:laravel-worker-default]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/genranks/current/artisan queue:work --queue=default --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=genranks
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/genranks/worker.log
stopwaitsecs=3600
```

**Create another worker configuration file**:

```bash
sudo nano /etc/supervisor/conf.d/laravel-worker-sequential.conf
```

```code
[program:laravel-worker-sequential]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/genranks/current/artisan queue:work --queue=sequential --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=genranks
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/genranks/worker-sequential.log
stopwaitsecs=3600
```

**Prepare Supervisor**:

```bash
sudo supervisorctl reread
sudo supervisorctl update
```

## Setup Cronjobs

```bash
crontab -e
```

**Add the following line**:

```code
* * * * * cd /var/www/genranks/current/ && php artisan schedule:run >> /dev/null 2>&1
```

## After uploading your project start the supervisor
```bash
sudo supervisorctl start laravel-worker-default:*,laravel-worker-sequential:*
```
