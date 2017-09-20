#!/bin/sh
#
export DATABASE__HOST=$MYSQL_PORT_3306_TCP_ADDR
export DATABASE__PORT=$MYSQL_PORT_3306_TCP_PORT
export DATABASE__NAME=$MYSQL_1_ENV_MYSQL_DATABASE
export DATABASE__USER=$MYSQL_1_ENV_MYSQL_USER
export DATABASE__PASSWORD=$MYSQL_1_ENV_MYSQL_PASSWORD

/bin/sh -c  "composer --ansi $@"