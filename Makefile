#path where application is installed
APPROOT = /home/chaosvpn_user/cvswork/servup/

#path where serv.php is
WWW = /var/www/cvpn/

JOBFILE = #!/bin/sh\n/usr/bin/python 

install:

	@echo ${WWW}

	cp serv.php ${WWW}/serv.php
	cp ui.css ${WWW}/ui.css
	mkdir ${WWW}/img
	cp img/*.png ${WWW}/img/
	chmod -R 755 ${WWW}/serv.php
	chmod -R 755 ${WWW}/ui.css
	chmod -R 744 ${WWW}/img/
	
	@echo ${JOBFILE}serv.py ${APPROOT}servList.csv ${APPROOT}servList.csv > ${APPROOT}job.sh



clean:
	rm ${WWW}/serv.php
	rm ${WWW}/ui.css
	rm -rf ${WWW}/img

