#
#  SpamAssassin maintenance for amavisd-new
#
# m h dom mon dow user  command
18 */3	* * *	amavis	test -e /usr/sbin/amavisd-new-cronjob && /usr/sbin/amavisd-new-cronjob sa-sync
