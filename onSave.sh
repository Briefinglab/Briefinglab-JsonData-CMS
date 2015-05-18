#!/bin/bash
################################################
#
#Syncronize folder plugin
#
################################################
# 
PLUGIN='.'
# 
DESTINATION='/mnt/www_dev/schiatti/wp-content/plugins/Briefinglab-JsonData-CMS'

rsync --delete --exclude-from 'exclude.txt' -avh $PLUGIN $DESTINATION
echo "OK"
