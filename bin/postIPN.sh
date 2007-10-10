#!/bin/sh

curl http://lobo.devel.civicrm.org/~lobo/drupal/sites/all/modules/civicrm/extern/ipn.php?reset=1\&module=contribute\&contactID=103\&contributionID=1\&contributionTypeID=2\&membershipID=27 -d mc_gross=100.00 -d txn_id=7RS96016KF572480S -d invoice=921860963317b0f98d6f8eac40d751d3 -d payment_status=Completed -d payment_fee=3.20