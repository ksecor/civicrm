#!/bin/sh

curl http://lobo.devel.civicrm.org/~lobo/drupal/sites/all/modules/civicrm/extern/ipn.php?reset=1\&module=contribute\&contactID=102\&contributionID=2 -d mc_gross=10.00 -d txn_id=5M6789701L0500744 -d invoice=ce0f5f51e390332c3b6579549bd950c0 -d payment_status=Completed -d payment_fee=0.59