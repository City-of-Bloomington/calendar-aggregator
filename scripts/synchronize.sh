#!/bin/bash
#
# @copyright 2023-2026 City of Bloomington, Indiana
# @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE
app_name=calendars
install_dir=/srv/sites/$app_name
data_dir=/srv/data/$app_name
log=/var/log/cron/$app_name

#----------------------------------------------------------
# Synchronize
#----------------------------------------------------------
SITE_HOME=$data_dir php $install_dir/scripts/synchronize.php >> $data_dir/sync.log 2>&1
chown -R www-data:staff $data_dir
chmod -R g+rwX $data_dir
