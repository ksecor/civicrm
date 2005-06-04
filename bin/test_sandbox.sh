#!/bin/bash -v

cd ../test/maxq

# This script is used to run maxq generated test scripts.
# Before running these scripts please see Common.py
# In Common.py comment and uncomment the constants as per the need
# Below commands use someof the modes of maxq. The modes used are : 
# The -r mode is for running maxq generated test scripts.
# The -q mode is for quite mode while testing.
# Instead of -q (quite mode), -d(debug mode) can be used.
# For all other options, fire $ maxq --help   


#############################
# Test for Viewing Contacts # 
#############################

maxq -q -r testViewContactIndividual.py testViewContactHousehold.py testViewContactOrganization.py

############################
# Test for Adding Contacts # 
############################

maxq -q -r testAddContactIndividual.py testAddContactHousehold.py testAddContactOrganization.py

#############################
# Test for Editing Contacts # 
#############################

maxq -q -r testEditContactIndividual.py testEditContactHousehold.py testEditContactOrganization.py

#############################################
# Test for Relationship By Relationship Tab # 
#############################################

maxq -q -r testViewRelByRelTab.py testEditRelByRelTab.py testAddRelByRelTab.py testDeleteRelByRelTab.py testDisableEnableRelByRelTab.py

########################################
# Test for Relationship By Contact Tab # 
########################################

maxq -q -r testViewRelByContactTab.py testEditRelByContactTab.py testAddRelByContactTab.py

###############################
# Test for Group By Group Tab # 
###############################

maxq -q -r testGroupAllByGroupTab.py testGroupAllByContactTab.py

#############################
# Test for Tags By Tags Tab # 
#############################

maxq -q -r testTagsAllByTagsTab.py

##############################
# Test for Notes By Note Tab # 
##############################

maxq -q -r testViewNoteByNoteTab.py testAddNoteByNoteTab.py testEditNoteByNoteTab.py testDeleteNoteByNoteTab.py

#################################
# Test for Notes By Contact Tab # 
#################################

maxq -q -r testAddNoteByContactTab.py testEditNoteByContactTab.py

########################
# Test for Custom Data # 
########################

maxq -r -q testCustomDataAllByTab.py

#######################
# Test for Admin Tags # 
#######################

maxq -q -r testAdminAddTags.py testAdminEditTags.py testAdminDeleteTags.py

##################################
# Test for Admin Mobile Provider # 
##################################

maxq -q -r testAdminAddMobileProvider.py testAdminEditMobileProvider.py testAdminEnableDisableMobileProvider.py

##############################
# Test for Admin IM Provider # 
##############################

maxq -q -r testAdminAddIMProvider.py testAdminEditIMProvider.py testAdminEnableDisableIMProvider.py

#####################################
# Test for Admin Relationship Types # 
#####################################

maxq -q -r testAdminViewRel.py testAdminAddRel.py testAdminEditRel.py testAdminEnableDisableRel.py

####################################
# Test for Admin Custom Data Field # 
####################################

maxq -q -r testAdminViewCustomDataField.py testAdminAddCustomDataField.py testAdminEditCustomDataField.py testAdminEnableDisableCustomDataField.py

####################################
# Test for Admin Custom Data Group # 
####################################

maxq -q -r testAdminAddCustomDataGroup.py testAdminEditCustomDataGroup.py testAdminEnableDisableCustomDataGroup.py

##########################
# Test for Basic Search  # 
##########################

maxq -q -r testSearchByLNameIndividual.py testSearchByHNameHousehold.py testSearchByONameOraganization.py testSearchByNoCriteria.py testSearchByGroup.py testSearchByContactTagGroupName.py 
