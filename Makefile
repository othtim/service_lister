	
#WWWROOT = /var/www/serv/
WWW = /var/www/cvpn/

install:

	@echo ${WWW}

	cp serv.php ${WWW}/serv.php
	cp ui.css ${WWW}/ui.css
	mkdir ${WWW}/img
	cp img/*.png ${WWW}/img/
	chmod -R 755 ${WWW}/serv.php
	chmod -R 755 ${WWW}/ui.css
	chmod -R 744 ${WWW}/img/
	
clean:
	rm ${WWW}/serv.php
	rm ${WWW}/ui.css
	rm -rf ${WWW}/img

