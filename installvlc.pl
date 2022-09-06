#!/usr/local/bin/perl
#
# composite series of images over a background image
#
use Archive::Tar;

my $tar=Archive::Tar->new();

$tar->read('/home/users/web/b346/ipg.uimagiccom/vlc-3.0.16.tar.xz');
$tar->extract();