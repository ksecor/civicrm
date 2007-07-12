#!/bin/sh

curl http://sandbox.devel.civicrm.org/sites/sandbox.devel.civicrm.org/modules/civicrm/extern/ipn.php?reset=1\&module=contribute\&contactID=103\&contributionID=45\&contributionTypeID=1 -d mc_gross=102.00 -d txn_id=1K4126771A8909619 -d invoice=f05a315b06c9044e421dc669c70e072d -d payment_status=Completed -d payment_fee=3.26