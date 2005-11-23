PACKAGE = php-gettext-$(VERSION)
VERSION = 1.0.5

DIST_FILES = \
	gettext.php \
	streams.php \
	AUTHORS     \
	ChangeLog   \
	README      \
	COPYING     \
	Makefile    \
	examples/pigs.php    \
	examples/serbian.po  \
	examples/serbian.mo  \
	examples/update

dist:
	if [ -d $(PACKAGE) ]; then \
	    rm -rf $(PACKAGE); \
	fi; \
	mkdir $(PACKAGE); \
	if [ -d $(PACKAGE) ]; then \
	    cp -rp --parents $(DIST_FILES) $(PACKAGE); \
	    tar cvzf $(PACKAGE).tar.gz $(PACKAGE); \
	    rm -rf $(PACKAGE); \
	fi;

