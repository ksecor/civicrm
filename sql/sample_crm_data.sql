-------------------------------------------------------
-------------------------------------------------------
---
--- sample data section
---
-------------------------------------------------------
-------------------------------------------------------

------------------------------------------------------
--- contact_domain
-------------------------------------------------------
---	id	name
---	1	'Domain 1'
---	2	'Domain 2'
---	3	'Domain 3'
-------------------------------------------------------

-------------------------------------------------------
--- contact_context
-------------------------------------------------------
---	id	domain_id	name		description
---	1	1		'Home'		''
---	2	1		'Work'		''
---	3	1		'Vacation Home'	''
-------------------------------------------------------

-------------------------------------------------------
--- contact
-------------------------------------------------------
---	id	uuid	rid	latest_rev	domain_id	contact_type	sort_name	source		pcm
---	1	24	1	1		1		'Household'	'Lobo\'s The'	'Source1'	'Email'
---	2	25	1	1		1		'Individual'	'Lobo Don'	'Source1'	'Email'
---	3	26	1	1		1		'Individual'	'Lobo Maya'	'Source1'	'Email'
---	4	27	1	1		1		'Organization'	'5am Bakery'	'Source1'	'Email'
-------------------------------------------------------

-------------------------------------------------------
--- contact_individual
-------------------------------------------------------
---	id	contact_uuid	contact_rid	first_name	middle_name	last_name	prefix
---	1	25		1		'Don'		'A'		'Lobo'		'Mr'
---	2	26		1		'Maya'		'D'		'Lobo'		'Ms'
---
-------------------------------------------------------

-------------------------------------------------------
--- contact_organization
-------------------------------------------------------
---	id	contact_uuid	contact_rid	org_name	legal_name	nick_name	sic_code	primary_contact_uuid
---	1	27		1		'5am Bakery'	'ABC Inc'	''		'B1'	 	25
---
-------------------------------------------------------

-------------------------------------------------------
--- contact_household
-------------------------------------------------------
---	id	contact_uuid	contact_rid	household_name	nick_name	primary_contact_uuid
---	1	24		1		'Lobo' 		'The Lobo\'s'	25
-------------------------------------------------------

-------------------------------------------------------
--- contact_address
-------------------------------------------------------
--- id	uuid rid domain_id line1       line2       city     sp_id zip5  usps_adc country_id address_org address_dept address_note is_shared
--- 1   1    1   1         'AddrLine1' 'AddrLine2' 'SFO'    1     94401 ''       11         ??          ??           ??           1
--- 2   2    1   1         'AddrLine1' 'AddrLine2' 'SSF'    1     94402 ''       11         ??          ??           ??           0
--- 3   3    1   1         'AddrLine1' 'AddrLine2' 'Carmel' 1     94403 ''       11         ??          ??           ??           1
-------------------------------------------------------

-------------------------------------------------------
--- contact_contact_address
-------------------------------------------------------
--- id uuid rid latest_rev contact_uuid address_uuid context_id is_primary
--- 1  1    1   1 	   24           1            1          1
--- 2  2    1   1 	   25           1            1          1
--- 3  3    1   1 	   26           1            1          1
--- 4  4    1   1 	   27           2            2          1
--- 5  5    1   1	   25		3	     3          0
--- 6  6    1   1	   26		3	     3          0
-------------------------------------------------------

-------------------------------------------------------
--- contact_email
-------------------------------------------------------
--- id uuid rid latest_rev contact_uuid email 		 context_id is_primary
--- 1  1    1   1	   25		'lobo@yahoo.com' 1          1
--- 2  2    1   1	   25		'lobo@gmail.com' 1          0
--- 3  3    1   1	   26		'maya@yahoo.com' 1          1
--- 4  4    1   1	   26		'maya@gmail.com' 1          0
--- 5  5    1   1	   27		'info@5am.com'   1          1
--- 6  6    1   1	   27		'lobo@5am.com'   1          0
-------------------------------------------------------

-------------------------------------------------------
--- contact_phone_mobile_providers
-------------------------------------------------------
--- id		name
--- 1		'ATT'
--- 2		'Sprint'
--- 3		'Verizon'
--- 4		'MCI'
--- 5		'PacBell'
-------------------------------------------------------

-------------------------------------------------------
--- contact_phone
-------------------------------------------------------
--- id uuid rid latest_rev contact_uuid number        number_stripped phone_type m_p_p_id context_id is_primary
--- 1  1    1   1          25           (650)111-1111 6501111111      'Phone'    	  1          1
--- 2  2    1   1          25           (650)222-2222 6502222222      'Mobile'   1 	  1          0
--- 3  3    1   1          26           (650)111-1111 6501111111      'Phone'    	  1          1
--- 4  4    1   1          26           (650)333-3333 6503333333      'Mobile'   1 	  1          0
--- 5  5    1   1          27           (650)444-4444 6504444444      'Phone'    	  2          1
--- 6  6    1   1          27           (650)555-5555 6505555555      'Phone'    	  2          0
--- 7  7    1   1          27           (650)666-6666 6506666666      'Fax'    	          2          1
--- 8  8    1   1          25           (650)777-7777 6507777777      'Phone'    	  3          1
--- 9  9    1   1          26           (650)777-7777 6507777777      'Phone'    	  3          1
-------------------------------------------------------

-------------------------------------------------------
--- contact_instant_message
-------------------------------------------------------
--- id uuid rid latest_rev contact_uuid screenname       context_id is_primary
--- 1  1    1   1          25		'lobo@yahoo.com' 1	    1
--- 2  2    1   1          25		'lobo@aim.com'   1	    0
--- 3  3    1   1          26		'maya@yahoo.com' 1	    1
--- 4  4    1   1          26		'maya@aim.com'   1	    0
--- 5  5    1   1          27		'5am@yahoo.com'  2	    1
--- 6  6    1   1          27		'5am@aim.com'    2	    1
--- 7  7    1   1          25		'lobo@irc.com'   2	    1
-------------------------------------------------------

-------------------------------------------------------
--- contact_relationship_types
-------------------------------------------------------
--- id domain_id name      	    description  	  direction        contact_type
--- 1  0         'parent'  	    'parent of'  	  'Unidirectional' 'individual'
--- 2  0         'child'   	    'child of'   	  'Unidirectional' 'individual'
--- 3  0         'sibling' 	    'sibling of' 	  'Bidirectional'  'individual'
--- 4  0         'household member' 'household member of' 'Unidirectional' 'individual'
--- 5  1         'NDN'              'next door neighbour' 'Bidirectional'  'Household'
-------------------------------------------------------

-------------------------------------------------------
--- contact_relationship
-------------------------------------------------------
--- id uuid rid latest_rev contact_uuid target_contact_uuid relationship_type_id
--- 1  1    1   1          25	        24                  4
--- 2  2    1   1          26	        24                  4
--- 3  3    1   1          25           26                  1
--- 4  4    1   1          26           25                  2
---
-------------------------------------------------------

-------------------------------------------------------
--- contact_task
-------------------------------------------------------
--- id uuid rid latest_rev target_contact_uuid assigned_contact_uuid time_started time_completed description
--- 1  1    1   1          25                  25                    'Nov-21'     'Nov-22'       'Update Donor Mailinglist'
------------------------------------------------------

-------------------------------------------------------
--- contact_note
-------------------------------------------------------
--- id uuid rid latest_rid contact_uuid note
--- 1  1    1   1          25           'In charge of 5k run'
-------------------------------------------------------








-------------------------------------------------------
--- Questions/Observations by yvb 21 Nov.
-------------------------------------------------------
---
--- 1. How can one determine if a person belongs to any household ?. Answered - contact_relationship tables.
---
--- 2. How does one get list of people from a particular household ?. Answered - contact_relationship tables.
---
--- 3. Clear defination of household ?
---    (my assumption is - all individals living in the same physical house
---     i.e. contacts having the same contact_address_uuid)
---
--- 4. Changing data in contact table will give rise to new revision.
---    for example if we change
---	2	25	1	1		1		'Individual'	'Lobo Don'	'Source1'	'Email'
---     to
---	2	25	2	1		1		'Individual'	'Lobo Don'	'Source1'	'Phone'
---
---     This would mean we need to change the row in contact_individual from
---	1	25		1		'Don'		'A'		'Lobo'		'Mr'
---     to
---	1	25		2		'Don'		'A'		'Lobo'		'Mr'
---
---     (since the latest revision is now pointing to Revision 2 in the contact table)
---
---     Is my assumption above correct ?
---
---
-------------------------------------------------------

