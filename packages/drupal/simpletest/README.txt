$Id: README.txt,v 1.12 2008/01/21 09:00:37 rokZlender Exp $

Description
-----------
A framework for running unit tests in Drupal.

USAGE WARNING
-------------
NEVER USE THIS MODULE IN A PRODUCTION ENVIRONMENT!
While running a test this module may (completely) change your Drupal setup.
Though it usually recovers the original setup at the end of a test, there is
no guarantee that it will work correctly, especially if an error or timeout
occurs.

Status
------
Drupal core tests are in the /tests subdirectory. We need people to help
expand our library of core tests, as well as provide tests for contributed
modules. 

Documentation
-------------
* Official SimpleTest documentation:
  http://simpletest.org/en/start-testing.html
* SimpleTest module API documentation:
  http://drupal.org/simpletest
* An Introduction to Unit Testing in Drupal:
  http://www.lullabot.com/articles/introduction-unit-testing
* A Drupal Module Developer's Guide to SimpleTest:
  http://www.lullabot.com/articles/drupal-module-developer-guide-simpletest

Module Authors
--------------
* Moshe Weitzman < weitzman at tejasa dot com >
* Kuba Zygmunt   < kuba.zygmunt at gmail dot com >
* Thomas Ilsche  < ThomasIlsche at gmx dot de >
* Rok Zlender    < rok.zlender at gmail dot com >

Thanks
------
* Google for sponsoring the initial test suite (and subsequent improvements)
  as Summer of Code projects:
  http://code.google.com/soc/
* Google and all students/mentors involved in the creation of a huge set of
  core tests during the Google Highly Open Participation contest:
  http://code.google.com/opensource/ghop/
