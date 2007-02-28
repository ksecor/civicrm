### DashBoard section ###
require 'test_new_individual'
require 'test_new_group' 
require 'test_manage_group'
require 'test_new_household'
require 'test_new_organization'
#require 'test_import_contacts'
#require 'test_civiContribute'
require 'test_civiMember'

#Individual section
require 'test_contact_contribution'
require 'test_contact_membership'
require 'test_contact_events'
require 'test_contact_activity'
require 'test_contact_relationship'
require 'test_contact_notes'
require 'test_contact_tags'
require 'test_find_contribution'
require 'test_find_membership'
require 'test_event_find_participants'
#require 'test_event_import_participants'

#Search section
require 'test_find_contacts' 
require 'test_advanced_search'
require 'test_search_builder' 

### Administer CiviCRM page ###

#Configure section
require 'test_admin_activity'
require 'test_admin_profile'
require 'test_admin_custom_data'
require 'test_admin_duplicate_matching'
require 'test_admin_location'
require 'test_admin_relationship_type.rb'
require 'test_admin_tag'
require 'test_admin_domain_information.rb'
require 'test_admin_option_group'
require 'test_admin_import_export_mapping'

#Setup section
require 'test_admin_gender'
require 'test_admin_IMProvider'
require 'test_admin_mobile_provider'
require 'test_admin_prefix'
require 'test_admin_suffix'
require 'test_admin_pref_comm_method'

#CiviContribute section
require 'test_admin_online_contribution_page'
require 'test_admin_manage_premium'
require 'test_admin_contribution_types'
require 'test_admin_payment_instrument'
require 'test_admin_accept_credit_card'

#CiviMember sction
require 'test_admin_membership_type'
require 'test_admin_membership_status'
require 'test_admin_message_templates'

#CiviEvent sction
require 'test_admin_manage_events'
require 'test_admin_event_type'
require 'test_admin_participant_role'
require 'test_admin_participant_status'
