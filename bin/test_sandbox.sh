#!/bin/bash -v

cd ../test/maxq

#maxq -r -q testViewContactIndividual.py 

#maxq -r -q testViewContactHousehold.py 

#maxq -r -q testViewContactOrganization.py 

#maxq -r -q testAddContactIndividual.py 

#maxq -r -q testAddContactHousehold.py 

#maxq -r -q testAddOrganization.py 

#maxq -r -q testEditContactIndividual.py

#maxq -r -q testEditContactHousehold.py

#maxq -r -q testEditContactOrganization.py

maxq -r -q testViewRelByRelTab.py

maxq -r -q testEditRelByRelTab.py

maxq -r -q testAddRelByRelTab.py

maxq -r -q testDeleteRelByRelTab.py

maxq -r -q testDisableEnableRelByRelTab.py

maxq -r -q testViewRelByContactTab.py

maxq -r -q testEditRelByContactTab.py

maxq -r -q testAddRelByContactTab.py



