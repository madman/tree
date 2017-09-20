#!/bin/bash

sed -i "s@listen = /var/run/php5-fpm.sock@listen = 9000@" /etc/php5/fpm/pool.d/www.conf

echo "env[APP_ENV] = docker" >> /etc/php5/fpm/pool.d/www.conf

#echo 'env[DATABASE__HOST] = $MYSQL_PORT_3306_TCP_ADDR' >> /etc/php5/fpm/pool.d/www.conf
#echo 'env[DATABASE__PORT] = $MYSQL_PORT_3306_TCP_PORT' >> /etc/php5/fpm/pool.d/www.conf
#echo 'env[DATABASE__DATABASE] = $MYSQL_1_ENV_MYSQL_DATABASE' >> /etc/php5/fpm/pool.d/www.conf
#echo 'env[DATABASE__USER] = $MYSQL_1_ENV_MYSQL_USER' >> /etc/php5/fpm/pool.d/www.conf
#echo 'env[DATABASE__PASSWORD] = $MYSQL_1_ENV_MYSQL_PASSWORD' >> /etc/php5/fpm/pool.d/www.conf

/bin/bash -l -c "$*"