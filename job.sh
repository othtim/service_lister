#!/bin/sh

# backup first
cp /home/chaosvpn_user/cvswork/servup/servList.csv /home/chaosvpn_user/cvswork/servup/backups/servList.csv.`date +%T%m%d%y`
cp /home/chaosvpn_user/cvswork/servup/servType.csv /home/chaosvpn_user/cvswork/servup/backups/servType.csv.`date +%T%m%d%y`

# do job
/usr/bin/python /home/chaosvpn_user/cvswork/servup/serv.py /home/chaosvpn_user/cvswork/servup/servList.csv /home/chaosvpn_user/cvswork/servup/servList.csv
