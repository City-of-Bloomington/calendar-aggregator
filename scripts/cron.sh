#!/bin/bash
# Creates a tarball containing a full snapshot of the data in the site
#
# @copyright Copyright 2011-2016 City of Bloomington, Indiana
# @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
APPLICATION_NAME=calendars
MYSQLDUMP=/usr/bin/mysqldump
MYSQL_CREDENTIALS=/etc/cron.daily/backup.d/$APPLICATION_NAME.cnf
BACKUP_DIR=/srv/backups/$APPLICATION_NAME
APPLICATION_HOME=/srv/sites/$APPLICATION_NAME
SITE_HOME=/srv/data/$APPLICATION_NAME
LOG=/var/log/cron/$APPLICATION_NAME

MYSQL_DBNAME=$APPLICATION_NAME

# How many days worth of tarballs to keep around
num_days_to_keep=5

#----------------------------------------------------------
# No Editing Required below this line
#----------------------------------------------------------
now=`date +%s`
today=`date +%F`

#----------------------------------------------------------
# Synchronize
#----------------------------------------------------------
SITE_HOME=$SITE_HOME php $APPLICATION_HOME/scripts/synchronize.php &> /var/log/cron/$APPLICATION_NAME
chown -R www-data:staff $SITE_HOME/Google_Client

#----------------------------------------------------------
# Backup
#----------------------------------------------------------
if [ ! -d $BACKUP_DIR ]
	then mkdir $BACKUP_DIR
fi
cd $BACKUP_DIR

# Dump the database
$MYSQLDUMP --defaults-extra-file=$MYSQL_CREDENTIALS $MYSQL_DBNAME > $MYSQL_DBNAME.sql

# Copy any data directories into this directory, so they're backed up, too.
# For example, if we had a media directory....
#cp -R $APPLICATION_HOME/data/media $today/media

# Tarball the Data
tar czf $today.tar.gz $MYSQL_DBNAME.sql

# Purge any backup tarballs that are too old
for file in `ls`
do
	atime=`stat -c %Y $file`
	if [ $(( $now - $atime >= $num_days_to_keep*24*60*60 )) = 1 ]
	then
		rm $file
	fi
done
