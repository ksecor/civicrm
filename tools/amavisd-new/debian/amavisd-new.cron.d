#
#  SpamAssassin maintenance for amavisd-new
#
# m h dom mon dow user  command
18 */3	* * *	amavis	test -e /usr/bin/sa-learn && test -e /usr/sbin/amavisd-new && /usr/bin/sa-learn --rebuild >/dev/null 2>&1
