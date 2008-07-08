#!/usr/bin/perl -w
# -*- perl -*-

#
# $Id: Makefile.PL,v 1.10 2007/03/15 20:40:34 eserte Exp $
# Author: Slaven Rezic
#
# Copyright (C) 2002,2005,2006 Slaven Rezic. All rights reserved.
# This program is free software; you can redistribute it and/or
# modify it under the same terms as Perl itself.
#
# Mail: slaven@rezic.de
# WWW:  http://www.rezic.de/eserte/
#

use ExtUtils::MakeMaker;

my $ver = MY->parse_version("sf-upload");
my $changes_file = parse_recent_changelog();

WriteMakefile
  (
   'NAME'	   => 'sf-upload',
   'VERSION'       => $ver,
   'EXE_FILES'     => ['sf-upload'],
   'PREREQ_PM'     => { 'LWP::UserAgent'    => 0,
			'HTML::Tree'        => 0,
			'HTML::TreeBuilder' => 0,
			'HTML::Parser'      => 3,
			'Crypt::SSLeay'     => 0,
		      },

   'dist' => {'POSTOP'=>'-$(CHMOD) 644 $(DISTVNAME).tar$(SUFFIX)'},

   'ABSTRACT_FROM' => '',
   'AUTHOR'        => 'Slaven Rezic (slaven@rezic.de)',
  );

sub parse_recent_changelog {
    my $q_ver = quotemeta($ver);
    open(C, "Changes") or die $!;
    my $gather = 0;
    my $changes = "";
    while(<C>) {
	if (/^$q_ver(\s|$)/) {
	    $gather = 1;
	} elsif ($gather) {
	    if (/^($|\s)/) {
		$changes .= $_;
	    } else {
		$gather = 0;
	    }
	}
    }
    close C;
    if ($changes eq '') {
	warn "No change log for version $ver found.\n";
    }
    my $changes_file = "Changes.current";
    open(CO, "> $changes_file") or die "Can't write to $changes_file: $!";
    print CO $changes;
    close CO;
    $changes_file;
}

sub MY::postamble {
    my $postamble = '';

    if (defined $ENV{USER} && $ENV{USER} eq 'eserte' && $^O =~ /bsd/i && -f "../perl.release.mk") {
	$postamble .= <<'EOF';

SF_PROJECT=sf-upload
SF_PACKAGE=sf-upload

#SF_GROUPID=	47521
#SF_PACKAGEID=	40510

SF_CHANGESFILE=	Changes.current

.include "../perl.release.mk"
.include "../perl.cvs.mk"

EOF
    }

    $postamble;
}

__END__
