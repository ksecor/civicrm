#!/bin/bash -v

cd ../test/maxq

## To run Test Script uncomment following files..
## One can uncomment one or more than one file(s) to carry on the test.
## Also, please do not change the sequence of the files.
## The -r mode is for running maxq..hence please do not remove it
## The -q mode is for quite mode while testing..
## Instead of -q (quite mode), one can give -d(debug mode)
## For all other options, fire $ masq --help   

#maxq -r -q testViewContactIndividual.py 

#maxq -r -q testViewContactHousehold.py 

#maxq -r -q testViewContactOrganization.py 

#maxq -r -q testAddContactIndividual.py 

#maxq -r -q testAddContactHousehold.py 

#maxq -r -q testAddOrganization.py 

#maxq -r -q testEditContactIndividual.py

#maxq -r -q testEditContactHousehold.py

#maxq -r -q testEditContactOrganization.py

#maxq -r -q testViewRelByRelTab.py

#maxq -r -q testEditRelByRelTab.py

#maxq -r -q testAddRelByRelTab.py

#maxq -r -q testDeleteRelByRelTab.py

#maxq -r -q testDisableEnableRelByRelTab.py

#maxq -r -q testViewRelByContactTab.py

#maxq -r -q testEditRelByContactTab.py

#maxq -r -q testAddRelByContactTab.py

#maxq -r -q testGroupAllByGroupTab.py

#maxq -r -q testGroupAllByContactTab.py

#maxq -r -q testTagsAllByTagsTab.py

#maxq -r -q testViewNoteByNoteTab.py

#maxq -r -q testAddNoteByNoteTab.py

#maxq -r -q testEditNoteByNoteTab.py

#maxq -r -q testDeleteNoteByNoteTab.py

#maxq -r -q testAddNoteByContactTab.py

#maxq -r -q testEditNoteByContactTab.py

#maxq -r -q testCustomDataAllByTab.py

#maxq -r -q testAdminAddTags.py

maxq -r -q testAdminEditTags.py

maxq -r -q testAdminDeleteTags.py
