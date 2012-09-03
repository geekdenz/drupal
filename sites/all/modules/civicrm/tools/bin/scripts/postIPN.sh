#!/bin/sh

curl http://crm_42/sites/crm_42/modules/civicrm/extern/ipn.php?reset=1\&module=event\&contactID=106\&participantID=54\&contributionID=16\&eventID=7 -d mc_gross=220.00 -d txn_id=5M6789701L0511744 -d invoice=ae9eaba77439f1bf712fb71f24e7a18b -d payment_status=Completed -d payment_fee=11.00