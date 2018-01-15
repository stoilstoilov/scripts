#!/bin/bash
now='$(date +"_%m_%d_%Y_%H-%M-%S")'

mkdir ~/backups

echo -n "DB Name:"
read db_name

echo -n "DB User:"
read db_user

echo -n "DB Pass:"
read db_pass

echo -n "Web root:"
read web_root

echo -n "CRON Mask:"
read cron_mask

crontab -l > /tmp/tempcrontab

echo "$cron_mask     mysqldump -u$db_user -p$db_pass $db_name | gzip > ~/backups/$db_name$now.sql.gz" >> /tmp/tempcrontab
echo "$cron_mask     tar czvf ~/backups/webroot$now.tar.gz $web_root" >> /tmp/tempcrontab

crontab -i /tmp/tempcrontab

rm /tmp/tempcrontab