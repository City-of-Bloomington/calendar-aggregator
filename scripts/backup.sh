#!/bin/bash
# Creates a tarball containing a full snapshot of the data in the site
#
# @copyright Copyright 2011-2023 City of Bloomington, Indiana
# @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE
app_name=calendars
mysql_credentials=/etc/mysql/debian.cnf
backup_dir=/srv/backups/$app_name
install_dir=/srv/sites/$app_name
dbname=$app_name

# How many days worth of tarballs to keep around
num_days_to_keep=5

#----------------------------------------------------------
# No Editing Required below this line
#----------------------------------------------------------
now=`date +%s`
today=`date +%F`

#----------------------------------------------------------
# Backup
#----------------------------------------------------------
if [ ! -d $backup_dir ]
	then mkdir $backup_dir
fi
cd $backup_dir

# Dump the database
mysqldump --defaults-extra-file=$mysql_credentials $dbname > $dbname.sql

# Copy any data directories into this directory, so they're backed up, too.
# For example, if we had a media directory....
#cp -R $install_dir/data/media $today/media

# Tarball the Data
tar czf $today.tar.gz $dbname.sql

# Purge any backup tarballs that are too old
for file in `ls`
do
	atime=`stat -c %Y $file`
	if [ $(( $now - $atime >= $num_days_to_keep*24*60*60 )) = 1 ]
	then
		rm $file
	fi
done
