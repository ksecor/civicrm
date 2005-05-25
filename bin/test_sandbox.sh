#!/bin/bash -v

cd ../test/maxq


maxq -r -q testViewContactIndividual.py 

maxq -r -q testViewContactHousehold.py 

maxq -r -q testViewContactOrganization.py 

maxq -r -q testAddContactIndividual.py 

maxq -r -q testAddContactHousehold.py 

maxq -r -q testAddOrganization.py 

maxq -r -q testEditContactIndividual.py

maxq -r -q testEditContactHousehold.py

maxq -r -q testEditContactOrganization.py
