#!/bin/bash -v

cd ../test/maxq

## To run Test Script uncomment following files..
## One can uncomment one or more than one file(s) to carry on the test.
## Also, please do not change the sequence of the files.
## The -r mode is for running maxq..hence please do not remove it
## The -q mode is for quite mode while testing..
## Instead of -q (quite mode), one can give -d(debug mode)
## For all other options, fire $ maxq --help   


#############################
# Test for Viewing Contacts # 
#############################

maxq -q -r testViewContactIndividual.py testViewContactHousehold.py testViewContactOrganization.py

############################
# Test for Adding Contacts # 
############################

maxq -r -q testAddContactIndividual.py testAddContactHousehold.py testAddContactOrganization.py

#############################
# Test for Editing Contacts # 
#############################

maxq -r -q testEditContactIndividual.py testEditContactHousehold.py testEditContactOrganization.py

#############################################
# Test for Relationship By Relationship Tab # 
#############################################

maxq -r -q testViewRelByRelTab.py testEditRelByRelTab.py testAddRelByRelTab.py testDeleteRelByRelTab.py testDisableEnableRelByRelTab.py

########################################
# Test for Relationship By Contact Tab # 
########################################

maxq -r -q testViewRelByContactTab.py testEditRelByContactTab.py testAddRelByContactTab.py

###############################
# Test for Group By Group Tab # 
###############################

maxq -r -q testGroupAllByGroupTab.py testGroupAllByContactTab.py

#############################
# Test for Tags By Tags Tab # 
#############################

maxq -r -q testTagsAllByTagsTab.py

##############################
# Test for Notes By Note Tab # 
##############################

maxq -r -q testViewNoteByNoteTab.py testAddNoteByNoteTab.py testEditNoteByNoteTab.py testDeleteNoteByNoteTab.py

#################################
# Test for Notes By Contact Tab # 
#################################

maxq -r -q testAddNoteByContactTab.py testEditNoteByContactTab.py

########################
# Test for Custom Data # 
########################

maxq -r -q testCustomDataAllByTab.py

#######################
# Test for Admin Tags # 
#######################

maxq -r -q testAdminAddTags.py testAdminEditTags.py testAdminDeleteTags.py

##################################
# Test for Admin Mobile Provider # 
##################################

maxq -r -q testAdminAddMobileProvider.py testAdminEditMobileProvider.py testAdminEnableDisableMobileProvider.py

##############################
# Test for Admin IM Provider # 
##############################

maxq -r -q testAdminAddIMProvider.py testAdminEditIMProvider.py testAdminEnableDisableIMProvider.py

#####################################
# Test for Admin Relationship Types # 
#####################################

maxq -r -q testAdminViewRel.py testAdminAddRel.py testAdminEditRel.py testAdminEnableDisableRel.py

####################################
# Test for Admin Custom Data Field # 
####################################

maxq -r -q testAdminViewCustomDataField.py testAdminAddCustomDataField.py testAdminEditCustomDataField.py testAdminEnableDisableCustomDataField.py

####################################
# Test for Admin Custom Data Group # 
####################################

maxq -r -q testAdminAddCustomDataGroup.py testAdminEditCustomDataGroup.py testAdminEnableDisableCustomDataGroup.py
