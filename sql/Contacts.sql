
/*******************************************************
*
* create_schema.sql
*
*    This script creates the schema for CRM
*    (contact relationship management system).
*
* Rev 11/15/2004:
*	Resolve InnoDB-MyISAM FK issue for now by leaving FK's to users
*		table as implicit
*	Created_by is now explicit FK to contact table.
*	Added contact_phone.phone_type (ENUM).
*	Eliminated contact_phone_mobile_provider table.
*	New/revised tables: contact_user, contact_task, contact_ext_data
*		contact_organization, contact_family, contact_note
*	Explicit naming for FK cols
*	rid_latest renamed to latest_rev, changed BOOLEAN
*
* Rev 11/11/2004:
*	Added domain heirarchy (for top-level contact and
*	 app/config tables
*	 now include domain_id (domain_id))
*	All PKs, revisioning cols assigned same names to
*	 facilitate data-object library dev
*	Added created_by (uid) to all user-editable tables
*	Replaced shareable contact_communications records
*	 with sharable contact_address records (this structure
*	 provides better support for required unique and
*    non-shared primary email addresses)
*
*	Revisioning structures clarified. 1:1 object extension
*	 records (e.g. contact_individual) will 'share' revision
*	 id with parent (contact). A change in either will
*	 force new revision. 1:many child types (e.g. contact_email)
*	 will carry their own revision id (+ latest_rev...)
*	 and will be revisioned independently.
*
*	Context is now indexed via contact_context to allow
*	 structured grouping of context data (e.g. home address,
*	 home phone, home email...).
*	New tables: contact_state_province, contact_country,
*	 contact_role_type, contact_role
*
*******************************************************/

/*******************************************************
*
* CREATE DATABASE XXX DEFAULT CHARACTER SET utf8 COLLATION utf8_bin
* the above changes are important for mysql4.1 else we get errors
* with pma
* [removed this - tables added to drupal DB for now]
*******************************************************/

/*******************************************************
*
* CREATE TABLES
*
*******************************************************/
/*******************************************************
*
* users
*
* Must create users table if running this against a
* standalone DB (i.e. NOT the drupal DB). Else, leave
* commented out.
*
*******************************************************/
/* DROP TABLE IF EXISTS users;
CREATE TABLE IF NOT EXISTS users (
  uid int(10) unsigned NOT NULL default '0',
  name varchar(60) NOT NULL default '',
  pass varchar(32) NOT NULL default '',
  mail varchar(64) default '',
  `mode` tinyint(1) NOT NULL default '0',
  sort tinyint(1) default '0',
  threshold tinyint(1) default '0',
  theme varchar(255) NOT NULL default '',
  signature varchar(255) NOT NULL default '',
  created int(11) NOT NULL default '0',
  `changed` int(11) NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '0',
  timezone varchar(8) default NULL,
  language varchar(12) NOT NULL default '',
  picture varchar(255) NOT NULL default '',
  init varchar(64) default '',
  `data` longtext,
  PRIMARY KEY  (uid),
  UNIQUE KEY name (name),
  KEY `changed` (`changed`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
*/

/*******************************************************
*
* contact_country
*
*******************************************************/
DROP TABLE IF EXISTS contact_country;
CREATE TABLE contact_country (

	id INT UNSIGNED NOT NULL COMMENT 'country id',
	name  VARCHAR(255),
	iso_code CHAR(2),

	country_code VARCHAR(5) COMMENT 'national prefix to be used when dialing TO this country',
	idd_prefix   VARCHAR(5) COMMENT 'international direct dialing prefix from within the country TO another country',
	ndd_prefix   VARCHAR(5) COMMENT 'access prefix to call within a country to a different area',

  PRIMARY KEY(id)

) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;



/*******************************************************
*
* contact_state_province
*
*******************************************************/
DROP TABLE IF EXISTS contact_state_province;
CREATE TABLE contact_state_province (

	id INT UNSIGNED NOT NULL COMMENT 'state_province id',
	name  VARCHAR(255),
	abbreviation  VARCHAR(10),
	countryid INT UNSIGNED NOT NULL,

	PRIMARY KEY(id),
	FOREIGN KEY(countryid) REFERENCES contact_country(id) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;



/*******************************************************
*
* contact_domain
*
* Top-level hierarchy (to support multi-org/domain installations.
*
*******************************************************/
DROP TABLE IF EXISTS contact_domain;
CREATE TABLE contact_domain (

	id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'domain id',

	rid INT UNSIGNED NOT NULL COMMENT 'domain revision id',
	latest_rev BOOLEAN NOT NULL DEFAULT 1 COMMENT 'is this record the latest revision',

	name VARCHAR(255) COMMENT 'domain/org name',

	is_deleted BOOLEAN NOT NULL DEFAULT 0 COMMENT 'is this entry deleted ?',
	created TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'time it was added',
	created_by INT UNSIGNED NOT NULL COMMENT 'contact id of person creating this revision',

	PRIMARY KEY (id, rid)
-- Must create FK after contact table is created (circular reference) 
-- FOREIGN KEY (created_by) REFERENCES contact(id)

) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin COMMENT='define domains for multi-org installs, else all contacts belong to domain 1';


/*******************************************************
*
* contact_context
*
*******************************************************/
DROP TABLE IF EXISTS contact_context;
CREATE TABLE contact_context (

	id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'context id',
	domain_id  INT UNSIGNED NOT NULL COMMENT 'which organization/domain owns this context',

	context  VARCHAR(255),

	PRIMARY KEY (id),
	FOREIGN KEY (domain_id) REFERENCES contact_domain(id) ON DELETE CASCADE ON UPDATE CASCADE,
	INDEX context_domain (domain_id)

) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin COMMENT='domain-level set of available contexts (e.g. Home, Work, Other...)';


/*******************************************************
*
* contact
*
*******************************************************/
DROP TABLE IF EXISTS contact;
CREATE TABLE contact (

	id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'contact id',
	rid INT UNSIGNED NOT NULL COMMENT 'contact revision id',
	latest_rev BOOLEAN NOT NULL DEFAULT 1 COMMENT 'is this record the latest revision',

	domain_id  INT UNSIGNED NOT NULL COMMENT 'which organization/domain owns this contact',

	contact_type ENUM('Individual','Organization','Family') COMMENT 'type of contact',
	sort_name VARCHAR(255) COMMENT 'name for sorting purposes',
	source VARCHAR(255) COMMENT 'where domain_id contact come from, e.g. import, donate module insert...',

-- contact-level communication permissions and preferences
	preferred_communication_method ENUM('Phone', 'Email', 'Postal Mail') COMMENT 'what is the preferred mode of communication',
	do_not_phone     BOOL DEFAULT 0,
	do_not_email     BOOL DEFAULT 0,
	do_not_mail      BOOL DEFAULT 0,

-- ? does the hash col give us a unique post/get param handle for the record that isn't easily hackable?
	hash INT UNSIGNED NOT NULL COMMENT 'key for hashing the entry',

--  Need to flesh out approach for linking module actions to contact. Commented out for now. dgg
--	caid_latest INT UNSIGNED COMMENT 'latest contact action id',

-- ? Why is this needed? dgg
--	module VARCHAR(255) COMMENT 'which module is handling this type',

	is_deleted BOOLEAN NOT NULL DEFAULT 0 COMMENT 'is this entry deleted ?',
	created TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'time it was added',
	created_by INT UNSIGNED NOT NULL COMMENT 'contact id of person creating this revision',

	PRIMARY KEY (id, rid),
	FOREIGN KEY (domain_id) REFERENCES contact_domain(id) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (created_by) REFERENCES contact(id),
	INDEX contact_domain (domain_id),
	INDEX index_sort_name (sort_name(30))

) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin COMMENT='primary record for contacts';

ALTER TABLE contact_domain ADD FOREIGN KEY (created_by) REFERENCES contact(id);

/*******************************************************
*
* contact_individual
*
*******************************************************/
DROP TABLE IF EXISTS contact_individual;
CREATE TABLE contact_individual(

	id  INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'table record id (i.e. contact_individual record id-not FK)',

-- contact_id+rid = contact.id+contact.rid gets a revision row for a contact with type=individual
-- revision to contact_individual values forces revisioning of parent contact record

	contact_id INT UNSIGNED NOT NULL COMMENT 'contact id FK',
	revision_id INT UNSIGNED NOT NULL COMMENT 'contact revision id FK',

	first_name VARCHAR(255) NOT NULL COMMENT 'first name',
	middle_name VARCHAR(255) COMMENT 'middle name',
	last_name VARCHAR(255) NOT NULL COMMENT 'last name',

	prefix VARCHAR(64) COMMENT 'prefix',
	suffix VARCHAR(64) COMMENT 'suffix',
	job_title VARCHAR(255) COMMENT 'optional job title for contact',

-- greeting_type constants:
	-- Formal: Prefix + first_name + last_name
	-- Informal: first_name
	-- Honorific: prefix + ?? (not sure how this is supposed to work - check w/ Bob Schmitt)
	-- Custom: greeting is stored in custom_greeting column
	greeting_type ENUM('Formal', 'Informal', 'Honorific', 'Custom') COMMENT 'preferred greeting format',
	custom_greeting VARCHAR(255) COMMENT 'custom greeting message',

	PRIMARY KEY (id, contact_id, revision_id),

	FOREIGN KEY (contact_id, revision_id) REFERENCES contact(id, rid) ON DELETE CASCADE ON UPDATE CASCADE
--	INDEX contact_individual (contact_id, revision_id)

) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin COMMENT='extends contact for type=individual';


/*******************************************************
*
* contact_organization
*
*******************************************************/
DROP TABLE IF EXISTS contact_organization;
CREATE TABLE contact_organization(

	id  INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'table record id',

-- contact_id+rid = contact.id+contact.rid gets a revision row for a contact with type=organization
-- revision to contact_organization values forces revisioning of parent contact record

	contact_id INT UNSIGNED NOT NULL COMMENT 'contact id FK',
	revision_id INT UNSIGNED NOT NULL COMMENT 'contact revision id FK',

	organization_name VARCHAR(255) NOT NULL,
	legal_name VARCHAR(255),
	nick_name VARCHAR(255),
	sic_code VARCHAR(64),
	primary_contact_id INT UNSIGNED COMMENT 'optional FK to primary contact for this org',

	PRIMARY KEY (id, contact_id, revision_id),

	FOREIGN KEY (contact_id, revision_id) REFERENCES contact(id, rid) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (primary_contact_id) REFERENCES contact(id)
--	INDEX contact_organization (contact_id, rid)

) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin COMMENT='extends contact for type=organization';


/*******************************************************
*
* contact_family
*
*******************************************************/
DROP TABLE IF EXISTS contact_family;
CREATE TABLE contact_family(

	id  INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'table record id',

-- contact_id+rid = contact.id+contact.rid gets a revision row for a contact with type=family
-- revision to contact_organization values forces revisioning of parent contact record

	contact_id INT UNSIGNED NOT NULL COMMENT 'contact id FK',
	revision_id INT UNSIGNED NOT NULL COMMENT 'contact revision id FK',

	family_name VARCHAR(255) NOT NULL COMMENT 'actual surname, e.g. Smith',
	nick_name VARCHAR(255) COMMENT 'e.g. The Smiths',
	primary_contact_id INT UNSIGNED COMMENT 'optional FK to primary contact for this family',

	PRIMARY KEY (id, contact_id, revision_id),

	FOREIGN KEY (contact_id, revision_id) REFERENCES contact(id, rid) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (primary_contact_id) REFERENCES contact(id)
--	INDEX contact_family (contact_id, revision_id)

) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin COMMENT='extends contact for type=family';


/*******************************************************
*
* contact_address
* physical locations (may be shared between contacts)
*******************************************************/
DROP TABLE IF EXISTS contact_address;
CREATE TABLE contact_address(

	id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'table record id',
	rid INT UNSIGNED NOT NULL COMMENT 'contact_address revision id',
	latest_rev BOOLEAN NOT NULL DEFAULT 1 COMMENT 'is this record the latest revision',

	domain_id  INT UNSIGNED NOT NULL COMMENT 'which organization/domain owns this contact_address',

	line1 VARCHAR(255) COMMENT 'address line 1',
	line2 VARCHAR(255) COMMENT 'address line 2',
	city VARCHAR(255) COMMENT 'city',
	county VARCHAR(255),
	state_province_id INT UNSIGNED NOT NULL COMMENT 'FK to contact_state_province table',

-- Is it useful to store US and non-US postal codes separately?
	zip5 INT UNSIGNED COMMENT 'zipcode - 5 digit',
	zip4 INT UNSIGNED COMMENT 'zipcode +4 segment',
-- US Postal Svc bulk mail address code
	usps_adc VARCHAR(64),

	postal_code VARCHAR(255) COMMENT 'other types of postal codes - non us',
	country_id INT UNSIGNED COMMENT 'index to contact_country table',

	address_organization VARCHAR(255) COMMENT 'organization name for mailing address',
	address_department VARCHAR(255) COMMENT 'department name for mailing address',
	address_note VARCHAR(255) COMMENT 'optional misc info (e.g. delivery instructions) for this address',

	is_shared BOOLEAN NOT NULL DEFAULT 0 COMMENT 'is this address shared between contacts (allows shortcut logic for user alerts on change)',

	is_deleted BOOLEAN NOT NULL DEFAULT 0 COMMENT 'is this entry deleted ?',
	created TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'time it was created',
	created_by INT UNSIGNED NOT NULL COMMENT 'contact id of person creating this revision',

	PRIMARY KEY (id, rid),

	FOREIGN KEY (domain_id) REFERENCES contact_domain(id) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (created_by) REFERENCES contact(id),
	FOREIGN KEY(state_province_id) REFERENCES contact_state_province(id),
	FOREIGN KEY(country_id) REFERENCES contact_country(id)

) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin COMMENT='Contact addresses (sharable by multiple contacts).';


/*******************************************************
*
* contact_contact_address
* Joins contacts to addresses and sets context (allows sharing of addresses between contacts)
*******************************************************/
DROP TABLE IF EXISTS contact_contact_address;
CREATE TABLE contact_contact_address(


	id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'table record id',
	rid INT UNSIGNED NOT NULL COMMENT 'revision id',
	latest_rev BOOLEAN NOT NULL DEFAULT 1 COMMENT 'is this record the latest revision',

	contact_id INT UNSIGNED NOT NULL COMMENT 'contact id',
	address_id INT UNSIGNED NOT NULL COMMENT 'contact_address id',

	context_id INT UNSIGNED COMMENT 'fk to contact_context (e.g. Home, Work...)',
	is_primary BOOLEAN NOT NULL DEFAULT 0 COMMENT 'primary mailing address for this contact (app allows 1 primary for each contact-communication type)',

	is_deleted BOOLEAN NOT NULL DEFAULT 0 COMMENT 'is this entry deleted ?',
	created TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'time it was created',
	created_by INT UNSIGNED NOT NULL COMMENT 'contact id of person creating this revision',

	PRIMARY KEY(id, rid),
	FOREIGN KEY (contact_id) REFERENCES contact(id) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (address_id) REFERENCES contact_address(id) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (created_by) REFERENCES contact(id),
	FOREIGN KEY (context_id) REFERENCES contact_context(id)

) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin COMMENT='joins contacts to (shareable) address locations; defines context for each';



/*******************************************************
*
* contact_email
*
*******************************************************/
DROP TABLE IF EXISTS contact_email;
CREATE TABLE contact_email(

	id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'table record id',
	rid INT UNSIGNED NOT NULL COMMENT 'revision id',
	latest_rev BOOLEAN NOT NULL DEFAULT 1 COMMENT 'is this record the latest revision',

	contact_id INT UNSIGNED NOT NULL COMMENT 'contact id',
	email VARCHAR(255) COMMENT 'email address',

	context_id INT UNSIGNED COMMENT 'fk to contact_context (e.g. Home, Work...)',
	is_primary BOOLEAN NOT NULL DEFAULT 0 COMMENT 'primary email address for this contact (app allows 1 primary for each contact-communication type)',

	is_deleted BOOLEAN NOT NULL DEFAULT 0 COMMENT 'is this entry deleted ?',
	created TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'time it was created',
	created_by INT UNSIGNED NOT NULL COMMENT 'contact id of person creating this revision',

	PRIMARY KEY(id, rid),
	FOREIGN KEY(contact_id) REFERENCES contact(id) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (created_by) REFERENCES contact(id),
	FOREIGN KEY (context_id) REFERENCES contact_context(id),
	INDEX email_contact (contact_id)

) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin COMMENT='contact email';



/*******************************************************
*
* contact_phone_mobile_providers
*
*******************************************************/
DROP TABLE IF EXISTS contact_phone_mobile_providers;
CREATE TABLE contact_phone_mobile_providers (

	id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'mobile provider id',
	name VARCHAR(255) COMMENT 'name of mobile provider',
	PRIMARY KEY(id)

) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin COMMENT='list of mobile phone providers';



/*******************************************************
*
* contact_phone
*
*******************************************************/
DROP TABLE IF EXISTS contact_phone;
CREATE TABLE contact_phone(

	id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'table record id',
	rid INT UNSIGNED NOT NULL COMMENT 'revision id',
	latest_rev BOOLEAN NOT NULL DEFAULT 1 COMMENT 'is this record the latest revision',

	contact_id INT UNSIGNED NOT NULL COMMENT 'contact id',

	number VARCHAR(255) COMMENT 'phone number',
-- ? what is this? same as 'number_striped' in Neil's schema ?
	number_canonical VARCHAR(255) COMMENT 'phone number',

	phone_type ENUM('Phone', 'Mobile', 'Fax', 'Pager') COMMENT 'what type of telecom device is this',
	mobile_phone_provider_id INT UNSIGNED COMMENT 'optional mobile provider id. Denormalized-not worth another table for 1 byte col.',

	context_id INT UNSIGNED COMMENT 'fk to contact_context (e.g. Home, Work...)',
	is_primary BOOLEAN NOT NULL DEFAULT 0 COMMENT 'primary email address for this contact (app allows 1 primary for each contact-communication type)',

	is_deleted BOOLEAN NOT NULL DEFAULT 0 COMMENT 'is this entry deleted ?',
	created TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'time it was created',
	created_by INT UNSIGNED NOT NULL COMMENT 'contact id of person creating this revision',

	PRIMARY KEY(id, rid),
	FOREIGN KEY(contact_id) REFERENCES contact(id) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (created_by) REFERENCES contact(id),
	FOREIGN KEY (context_id) REFERENCES contact_context(id),
	FOREIGN KEY (mobile_phone_provider_id) REFERENCES contact_phone_mobile_providers(id),
	INDEX phone_contact (contact_id)

) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin COMMENT='contact phone numbers (base table)';



/*******************************************************
*
* contact_instant_message
*
*******************************************************/
DROP TABLE IF EXISTS contact_instant_message;
CREATE TABLE contact_instant_message(

	id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'table record id',
	rid INT UNSIGNED NOT NULL COMMENT 'revision id',
	latest_rev BOOLEAN NOT NULL DEFAULT 1 COMMENT 'is this record the latest revision',

	contact_id INT UNSIGNED NOT NULL COMMENT 'contact id',

	screenname VARCHAR(255) COMMENT 'messenger id',

	context_id INT UNSIGNED COMMENT 'fk to contact_context (e.g. Home, Work...)',
	is_primary BOOLEAN NOT NULL DEFAULT 0 COMMENT 'primary email address for this contact (app allows 1 primary for each contact-communication type)',

	is_deleted BOOLEAN NOT NULL DEFAULT 0 COMMENT 'is this entry deleted ?',
	created TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'time it was created',
	created_by INT UNSIGNED NOT NULL COMMENT 'contact id of person creating this revision',

	PRIMARY KEY(id, rid),
	FOREIGN KEY(contact_id) REFERENCES contact(id) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (created_by) REFERENCES contact(id),
	FOREIGN KEY (context_id) REFERENCES contact_context(id),
	INDEX im_contact (contact_id)

) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin COMMENT='contact instant message';



/*******************************************************
*
* contact_relationship_types
*
* Several default types (e.g. parent, child, sibling...
* are included by default). Admins will be able to add
* types (for a domain).
* 
*******************************************************/
DROP TABLE IF EXISTS contact_relationship_types;
CREATE TABLE contact_relationship_types(

	id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'contact relationship type id',

	domain_id  INT UNSIGNED NOT NULL COMMENT 'which organization/domain owns this type',

	direction ENUM('Unidirectional', 'Bidirectional') COMMENT 'relationship cardinality',

	name VARCHAR(255) COMMENT 'name of the relationship',
	description VARCHAR(255) COMMENT 'description of the relationship',

	is_deleted BOOLEAN NOT NULL DEFAULT 0 COMMENT 'is this entry deleted ?',
	created TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'time it was created',
	created_by INT UNSIGNED NOT NULL COMMENT 'contact id of person creating this revision',

	PRIMARY KEY(id),
	FOREIGN KEY (domain_id) REFERENCES contact_domain(id) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (created_by) REFERENCES contact(id),
	INDEX crt_domain (domain_id)

) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin COMMENT='contact relationship types';



/*******************************************************
*
* contact_relationship
*
*******************************************************/
DROP TABLE IF EXISTS contact_relationship;
CREATE TABLE contact_relationship(

	id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'contact relationship id',
	rid INT UNSIGNED NOT NULL COMMENT 'revision id',
	latest_rev BOOLEAN NOT NULL DEFAULT 1 COMMENT 'is this record the latest revision',

	contact_id INT UNSIGNED NOT NULL COMMENT 'contact id',
	target_contact_id INT UNSIGNED NOT NULL COMMENT 'target contact id',

	relationship_type_id INT UNSIGNED NOT NULL COMMENT 'contact relationship type id',

	is_deleted BOOLEAN NOT NULL DEFAULT 0 COMMENT 'is this entry deleted ?',
	created TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'time it was created',
	created_by INT UNSIGNED NOT NULL COMMENT 'contact id of person creating this revision',

	PRIMARY KEY(id),
	FOREIGN KEY(relationship_type_id) REFERENCES contact_relationship_types(id) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY(contact_id) REFERENCES contact(id) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY(target_contact_id) REFERENCES contact(id) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (created_by) REFERENCES contact(id)

) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin COMMENT='contact relationships';



/*******************************************************
*
* contact_task
*
*******************************************************/
DROP TABLE IF EXISTS contact_task;
CREATE TABLE contact_task(

	id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'table record id',
	rid INT UNSIGNED NOT NULL COMMENT 'revision id',
	latest_rev BOOLEAN NOT NULL DEFAULT 1 COMMENT 'is this record the latest revision',

-- ? This struct implies that all tasks have a target contact. What about 'mailings'
--		which target groups. How does this relate to other types of org tasks (e.g. "compose newsletter"...)?
	target_contact_id INT UNSIGNED NOT NULL COMMENT 'target contact id for task',
	assigned_contact_id INT UNSIGNED NOT NULL COMMENT 'task assigned to which contact',

	time_started DATETIME DEFAULT 0 COMMENT 'when was task started',
	time_completed DATETIME DEFAULT 0 COMMENT 'when was task completed',

	description VARCHAR(255) COMMENT 'description of task',

	is_deleted BOOLEAN NOT NULL DEFAULT 0 COMMENT 'is this entry deleted ?',
	created TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'time it was created',
	created_by INT UNSIGNED NOT NULL COMMENT 'contact id of person creating this revision',

	PRIMARY KEY(id),
	FOREIGN KEY(target_contact_id) REFERENCES contact(id) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY(assigned_contact_id) REFERENCES contact(id) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (created_by) REFERENCES contact(id),
	INDEX task_contact(assigned_contact_id,target_contact_id)

) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin COMMENT='tasks related to a contact';


/*******************************************************
*
* contact_note
*
*******************************************************/
DROP TABLE IF EXISTS contact_note;
CREATE TABLE contact_note(

	id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'table record id',
	rid INT UNSIGNED NOT NULL COMMENT 'revision id',
	latest_rev BOOLEAN NOT NULL DEFAULT 1 COMMENT 'is this record the latest revision',

	contact_id INT UNSIGNED NOT NULL COMMENT 'note is about this contact',

	note TEXT COMMENT 'note or comment',

	is_deleted BOOLEAN NOT NULL DEFAULT 0 COMMENT 'is this entry deleted ?',
	created TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'time it was created',
	created_by INT UNSIGNED NOT NULL COMMENT 'contact id of person creating this revision',

	PRIMARY KEY(id),
	FOREIGN KEY(contact_id) REFERENCES contact(id) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (created_by) REFERENCES contact(id),
	INDEX note_contact(contact_id)

) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin COMMENT='multiple notes/comments related to a contact';



/*******************************************************
*
* extended property tables (TBD)
*	contact_ext_property
*	contact_ext_property_validation_rule (? separate table vs. defined in contact_ext_property
*
* ? Should we name/model these form/display tables for use with extended properties only?
*		Or use generic naming on assmption we well extend them to defined
*		all form elements? Is validation a 
*	contact_ext_property_form (contact_form)
*	contact_ext_property_form_component (contact_form_component)
*	contact_ext_property_form_field (contact_form_field)
*	contact_ext_property_group (contact_form_group) etc.
*	contact_ext_property_option
*
* Draft concept for form rendering tables:
*	- form_fields point to (or ARE) a contact_ext_property row
*	- form_groups contain one or more related form_fields
*	- forms are sets of form_components
*	- form_components may be form_fields, form_groups, or other forms
*
*******************************************************/

/*******************************************************
*
* contact_ext_data
*
*******************************************************/
DROP TABLE IF EXISTS contact_ext_data;
CREATE TABLE contact_ext_data(

	id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'table record id',
	rid INT UNSIGNED NOT NULL COMMENT 'revision id',
	latest_rev BOOLEAN NOT NULL DEFAULT 1 COMMENT 'is this record the latest revision',

	contact_id INT UNSIGNED NOT NULL COMMENT 'contact id',

	ext_property_id INT UNSIGNED NOT NULL COMMENT 'FK to contact_ext_property',

-- ? Do we need to know the 'group' (I think this is presentation info only and not needed here?)
--	ext_property_group_id  INT UNSIGNED NOT NULL COMMENT 'group id ( group is a named collection of fields )',

-- Data is stored in one of these 'buckets' depending on property type.
	int_data INT COMMENT 'stores data for ext property types = ?_int. This col supports signed integers.',
	float_data INT COMMENT 'stores data for ext property types = ?_float and $_money.',
	char_data VARCHAR(255) COMMENT 'data for ext property types = ?_text',
	date_data DATETIME COMMENT 'data for ext property types = ?_date',
	memo_data TEXT COMMENT 'data for ext property type = ?_memo',
-- ? Should we have separate storage bucket for BOOLEANS ? dgg

	is_deleted BOOLEAN NOT NULL DEFAULT 0 COMMENT 'is this entry deleted ?',
	created TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'time it was created',
	created_by INT UNSIGNED NOT NULL COMMENT 'contact id of person creating this revision',

	PRIMARY KEY(id, rid),
	FOREIGN KEY(contact_id) REFERENCES contact(id) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY(created_by) REFERENCES contact(id),
-- dgg Uncomment FK once related table is in place
-- FOREIGN KEY(ext_property_id) REFERENCES contact_ext_property(id),
	INDEX ext_data_contact (contact_id)

) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin COMMENT='stores the data for extended properties';


/*******************************************************
*
* contact_role (!! TBD)
* May use dynamic properties for roles (so they can be 
* flexible types and encapsulate 'sets' of data).
*	EX: role=board member
*		data=role_name, start date, end date
*
* Else, need defined role types to support various use cases.
*
*******************************************************/



/*******************************************************
*
* contact_role_type (!! TBD)
*
*******************************************************/



/*******************************************************
*
* contact_role (!! TBD)
*
*******************************************************/

/*******************************************************
*
* contact_county (!! TBD)
*
* Look at importing county tables (re: Advokit)
*
*******************************************************/



INSERT INTO contact_country (id, name,iso_code) VALUES("1001", "Afghanistan", "AF");
INSERT INTO contact_country (id, name,iso_code) VALUES("1002", "Albania", "AL");
INSERT INTO contact_country (id, name,iso_code) VALUES("1003", "Algeria", "DZ");
INSERT INTO contact_country (id, name,iso_code) VALUES("1004", "American Samoa", "AS");
INSERT INTO contact_country (id, name,iso_code) VALUES("1005", "Andorra", "AD");
INSERT INTO contact_country (id, name,iso_code) VALUES("1006", "Angola", "AO");
INSERT INTO contact_country (id, name,iso_code) VALUES("1007", "Anguilla", "AI");
INSERT INTO contact_country (id, name,iso_code) VALUES("1008", "Antarctica", "AQ");
INSERT INTO contact_country (id, name,iso_code) VALUES("1009", "Antigua and Barbuda", "AG");
INSERT INTO contact_country (id, name,iso_code) VALUES("1010", "Argentina", "AR");
INSERT INTO contact_country (id, name,iso_code) VALUES("1011", "Armenia", "AM");
INSERT INTO contact_country (id, name,iso_code) VALUES("1012", "Aruba", "AW");
INSERT INTO contact_country (id, name,iso_code) VALUES("1013", "Australia", "AU");
INSERT INTO contact_country (id, name,iso_code) VALUES("1014", "Austria", "AT");
INSERT INTO contact_country (id, name,iso_code) VALUES("1015", "Azerbaijan", "AZ");
INSERT INTO contact_country (id, name,iso_code) VALUES("1016", "Bahrain", "BH");
INSERT INTO contact_country (id, name,iso_code) VALUES("1017", "Bangladesh", "BD");
INSERT INTO contact_country (id, name,iso_code) VALUES("1018", "Barbados", "BB");
INSERT INTO contact_country (id, name,iso_code) VALUES("1019", "Belarus", "BY");
INSERT INTO contact_country (id, name,iso_code) VALUES("1020", "Belgium", "BE");
INSERT INTO contact_country (id, name,iso_code) VALUES("1021", "Belize", "BZ");
INSERT INTO contact_country (id, name,iso_code) VALUES("1022", "Benin", "BJ");
INSERT INTO contact_country (id, name,iso_code) VALUES("1023", "Bermuda", "BM");
INSERT INTO contact_country (id, name,iso_code) VALUES("1024", "Bhutan", "BT");
INSERT INTO contact_country (id, name,iso_code) VALUES("1025", "Bolivia", "BO");
INSERT INTO contact_country (id, name,iso_code) VALUES("1026", "Bosnia and Herzegovina", "BA");
INSERT INTO contact_country (id, name,iso_code) VALUES("1027", "Botswana", "BW");
INSERT INTO contact_country (id, name,iso_code) VALUES("1028", "Bouvet Island", "BV");
INSERT INTO contact_country (id, name,iso_code) VALUES("1029", "Brazil", "BR");
INSERT INTO contact_country (id, name,iso_code) VALUES("1030", "British Indian Ocean Territory", "IO");
INSERT INTO contact_country (id, name,iso_code) VALUES("1031", "British Virgin Islands", "VG");
INSERT INTO contact_country (id, name,iso_code) VALUES("1032", "Brunei Darussalam", "BN");
INSERT INTO contact_country (id, name,iso_code) VALUES("1033", "Bulgaria", "BG");
INSERT INTO contact_country (id, name,iso_code) VALUES("1034", "Burkina Faso", "BF");
INSERT INTO contact_country (id, name,iso_code) VALUES("1035", "Burma", "MM");
INSERT INTO contact_country (id, name,iso_code) VALUES("1036", "Burundi", "BI");
INSERT INTO contact_country (id, name,iso_code) VALUES("1037", "Cambodia", "KH");
INSERT INTO contact_country (id, name,iso_code) VALUES("1038", "Cameroon", "CM");
INSERT INTO contact_country (id, name,iso_code) VALUES("1039", "Canada", "CA");
INSERT INTO contact_country (id, name,iso_code) VALUES("1040", "Cape Verde", "CV");
INSERT INTO contact_country (id, name,iso_code) VALUES("1041", "Cayman Islands", "KY");
INSERT INTO contact_country (id, name,iso_code) VALUES("1042", "Central African Republic", "CF");
INSERT INTO contact_country (id, name,iso_code) VALUES("1043", "Chad", "TD");
INSERT INTO contact_country (id, name,iso_code) VALUES("1044", "Chile", "CL");
INSERT INTO contact_country (id, name,iso_code) VALUES("1045", "China", "CN");
INSERT INTO contact_country (id, name,iso_code) VALUES("1046", "Christmas Island", "CX");
INSERT INTO contact_country (id, name,iso_code) VALUES("1047", "Cocos (Keeling) Islands", "CC");
INSERT INTO contact_country (id, name,iso_code) VALUES("1048", "Colombia", "CO");
INSERT INTO contact_country (id, name,iso_code) VALUES("1049", "Comoros", "KM");
INSERT INTO contact_country (id, name,iso_code) VALUES("1050", "Congo, Democratic Republic of the", "CG");
INSERT INTO contact_country (id, name,iso_code) VALUES("1051", "Congo, Republic of the", "CF");
INSERT INTO contact_country (id, name,iso_code) VALUES("1052", "Cook Islands", "CK");
INSERT INTO contact_country (id, name,iso_code) VALUES("1053", "Costa Rica", "CR");
INSERT INTO contact_country (id, name,iso_code) VALUES("1054", "Cote d\'Ivoire", "CI");
INSERT INTO contact_country (id, name,iso_code) VALUES("1055", "Croatia", "HR");
INSERT INTO contact_country (id, name,iso_code) VALUES("1056", "Cuba", "CU");
INSERT INTO contact_country (id, name,iso_code) VALUES("1057", "Cyprus", "CY");
INSERT INTO contact_country (id, name,iso_code) VALUES("1058", "Czech Republic", "CZ");
INSERT INTO contact_country (id, name,iso_code) VALUES("1059", "Denmark", "DK");
INSERT INTO contact_country (id, name,iso_code) VALUES("1060", "Djibouti", "DJ");
INSERT INTO contact_country (id, name,iso_code) VALUES("1061", "Dominica", "DM");
INSERT INTO contact_country (id, name,iso_code) VALUES("1062", "Dominican Republic", "DO");
INSERT INTO contact_country (id, name,iso_code) VALUES("1063", "East Timor", "TP");
INSERT INTO contact_country (id, name,iso_code) VALUES("1064", "Ecuador", "EC");
INSERT INTO contact_country (id, name,iso_code) VALUES("1065", "Egypt", "EG");
INSERT INTO contact_country (id, name,iso_code) VALUES("1066", "El Salvador", "SV");
INSERT INTO contact_country (id, name,iso_code) VALUES("1067", "Equatorial Guinea", "GQ");
INSERT INTO contact_country (id, name,iso_code) VALUES("1068", "Eritrea", "ER");
INSERT INTO contact_country (id, name,iso_code) VALUES("1069", "Estonia", "EE");
INSERT INTO contact_country (id, name,iso_code) VALUES("1070", "Ethiopia", "ET");
INSERT INTO contact_country (id, name,iso_code) VALUES("1071", "European Union", "EU");
INSERT INTO contact_country (id, name,iso_code) VALUES("1072", "Falkland Islands (Islas Malvinas)", NULL);
INSERT INTO contact_country (id, name,iso_code) VALUES("1073", "Faroe Islands", "FO");
INSERT INTO contact_country (id, name,iso_code) VALUES("1074", "Fiji", "FJ");
INSERT INTO contact_country (id, name,iso_code) VALUES("1075", "Finland", "FI");
INSERT INTO contact_country (id, name,iso_code) VALUES("1076", "France", "FR");
INSERT INTO contact_country (id, name,iso_code) VALUES("1077", "French Guiana", "GF");
INSERT INTO contact_country (id, name,iso_code) VALUES("1078", "French Polynesia", "PF");
INSERT INTO contact_country (id, name,iso_code) VALUES("1079", "French Southern and Antarctic Lands", "TF");
INSERT INTO contact_country (id, name,iso_code) VALUES("1080", "Gabon", "GA");
INSERT INTO contact_country (id, name,iso_code) VALUES("1081", "Georgia", "GE");
INSERT INTO contact_country (id, name,iso_code) VALUES("1082", "Germany", "DE");
INSERT INTO contact_country (id, name,iso_code) VALUES("1083", "Ghana", "GH");
INSERT INTO contact_country (id, name,iso_code) VALUES("1084", "Gibraltar", "GI");
INSERT INTO contact_country (id, name,iso_code) VALUES("1085", "Greece", "GR");
INSERT INTO contact_country (id, name,iso_code) VALUES("1086", "Greenland", "GL");
INSERT INTO contact_country (id, name,iso_code) VALUES("1087", "Grenada", "GD");
INSERT INTO contact_country (id, name,iso_code) VALUES("1088", "Guadeloupe", "GP");
INSERT INTO contact_country (id, name,iso_code) VALUES("1089", "Guam", "GU");
INSERT INTO contact_country (id, name,iso_code) VALUES("1090", "Guatemala", "GT");
INSERT INTO contact_country (id, name,iso_code) VALUES("1091", "Guinea", "GN");
INSERT INTO contact_country (id, name,iso_code) VALUES("1092", "Guinea-Bissau", "GW");
INSERT INTO contact_country (id, name,iso_code) VALUES("1093", "Guyana", "GY");
INSERT INTO contact_country (id, name,iso_code) VALUES("1094", "Haiti", "HT");
INSERT INTO contact_country (id, name,iso_code) VALUES("1095", "Heard Island and McDonald Islands", "HM");
INSERT INTO contact_country (id, name,iso_code) VALUES("1096", "Holy See (Vatican City)", "VA");
INSERT INTO contact_country (id, name,iso_code) VALUES("1097", "Honduras", "HN");
INSERT INTO contact_country (id, name,iso_code) VALUES("1098", "Hong Kong (SAR)", "HK");
INSERT INTO contact_country (id, name,iso_code) VALUES("1099", "Hungary", "HU");
INSERT INTO contact_country (id, name,iso_code) VALUES("1100", "Iceland", "IS");
INSERT INTO contact_country (id, name,iso_code) VALUES("1101", "India", "IN");
INSERT INTO contact_country (id, name,iso_code) VALUES("1102", "Indonesia", "ID");
INSERT INTO contact_country (id, name,iso_code) VALUES("1103", "Iran", "IR");
INSERT INTO contact_country (id, name,iso_code) VALUES("1104", "Iraq", "IQ");
INSERT INTO contact_country (id, name,iso_code) VALUES("1105", "Ireland", "IE");
INSERT INTO contact_country (id, name,iso_code) VALUES("1106", "Israel", "IL");
INSERT INTO contact_country (id, name,iso_code) VALUES("1107", "Italy", "IT");
INSERT INTO contact_country (id, name,iso_code) VALUES("1108", "Jamaica", "JM");
INSERT INTO contact_country (id, name,iso_code) VALUES("1109", "Japan", "JP");
INSERT INTO contact_country (id, name,iso_code) VALUES("1110", "Jordan", "JO");
INSERT INTO contact_country (id, name,iso_code) VALUES("1111", "Kazakhstan", "KZ");
INSERT INTO contact_country (id, name,iso_code) VALUES("1112", "Kenya", "KE");
INSERT INTO contact_country (id, name,iso_code) VALUES("1113", "Kiribati", "KI");
INSERT INTO contact_country (id, name,iso_code) VALUES("1114", "Korea, North", "KP");
INSERT INTO contact_country (id, name,iso_code) VALUES("1115", "Korea, South", "KR");
INSERT INTO contact_country (id, name,iso_code) VALUES("1116", "Kuwait", "KW");
INSERT INTO contact_country (id, name,iso_code) VALUES("1117", "Kyrgyzstan", "KG");
INSERT INTO contact_country (id, name,iso_code) VALUES("1118", "Laos", "LA");
INSERT INTO contact_country (id, name,iso_code) VALUES("1119", "Latvia", "LV");
INSERT INTO contact_country (id, name,iso_code) VALUES("1120", "Lebanon", "LB");
INSERT INTO contact_country (id, name,iso_code) VALUES("1121", "Lesotho", "LS");
INSERT INTO contact_country (id, name,iso_code) VALUES("1122", "Liberia", "LR");
INSERT INTO contact_country (id, name,iso_code) VALUES("1123", "Libya", "LY");
INSERT INTO contact_country (id, name,iso_code) VALUES("1124", "Liechtenstein", "LI");
INSERT INTO contact_country (id, name,iso_code) VALUES("1125", "Lithuania", "LT");
INSERT INTO contact_country (id, name,iso_code) VALUES("1126", "Luxembourg", "LU");
INSERT INTO contact_country (id, name,iso_code) VALUES("1127", "Macao", "MO");
INSERT INTO contact_country (id, name,iso_code) VALUES("1128", "Macedonia, The Former Yugoslav Republic of", "MK");
INSERT INTO contact_country (id, name,iso_code) VALUES("1129", "Madagascar", "MG");
INSERT INTO contact_country (id, name,iso_code) VALUES("1130", "Malawi", "MW");
INSERT INTO contact_country (id, name,iso_code) VALUES("1131", "Malaysia", "MY");
INSERT INTO contact_country (id, name,iso_code) VALUES("1132", "Maldives", "MV");
INSERT INTO contact_country (id, name,iso_code) VALUES("1133", "Mali", "ML");
INSERT INTO contact_country (id, name,iso_code) VALUES("1134", "Malta", "MT");
INSERT INTO contact_country (id, name,iso_code) VALUES("1135", "Marshall Islands", "MH");
INSERT INTO contact_country (id, name,iso_code) VALUES("1136", "Martinique", "MQ");
INSERT INTO contact_country (id, name,iso_code) VALUES("1137", "Mauritania", "MR");
INSERT INTO contact_country (id, name,iso_code) VALUES("1138", "Mauritius", "MU");
INSERT INTO contact_country (id, name,iso_code) VALUES("1139", "Mayotte", "YT");
INSERT INTO contact_country (id, name,iso_code) VALUES("1140", "Mexico", "MX");
INSERT INTO contact_country (id, name,iso_code) VALUES("1141", "Micronesia, Federated States of", "FM");
INSERT INTO contact_country (id, name,iso_code) VALUES("1142", "Moldova", "MD");
INSERT INTO contact_country (id, name,iso_code) VALUES("1143", "Monaco", "MC");
INSERT INTO contact_country (id, name,iso_code) VALUES("1144", "Mongolia", "MN");
INSERT INTO contact_country (id, name,iso_code) VALUES("1145", "Montserrat", "MS");
INSERT INTO contact_country (id, name,iso_code) VALUES("1146", "Morocco", "MA");
INSERT INTO contact_country (id, name,iso_code) VALUES("1147", "Mozambique", "MZ");
INSERT INTO contact_country (id, name,iso_code) VALUES("1148", "Namibia", "NA");
INSERT INTO contact_country (id, name,iso_code) VALUES("1149", "Nauru", "NR");
INSERT INTO contact_country (id, name,iso_code) VALUES("1150", "Nepal", "NP");
INSERT INTO contact_country (id, name,iso_code) VALUES("1151", "Netherlands Antilles", "AN");
INSERT INTO contact_country (id, name,iso_code) VALUES("1152", "Netherlands", "NL");
INSERT INTO contact_country (id, name,iso_code) VALUES("1153", "New Caledonia", "NC");
INSERT INTO contact_country (id, name,iso_code) VALUES("1154", "New Zealand", "NZ");
INSERT INTO contact_country (id, name,iso_code) VALUES("1155", "Nicaragua", "NI");
INSERT INTO contact_country (id, name,iso_code) VALUES("1156", "Niger", "NE");
INSERT INTO contact_country (id, name,iso_code) VALUES("1157", "Nigeria", "NG");
INSERT INTO contact_country (id, name,iso_code) VALUES("1158", "Niue", "NU");
INSERT INTO contact_country (id, name,iso_code) VALUES("1159", "Norfolk Island", "NF");
INSERT INTO contact_country (id, name,iso_code) VALUES("1160", "Northern Mariana Islands", "MP");
INSERT INTO contact_country (id, name,iso_code) VALUES("1161", "Norway", "NO");
INSERT INTO contact_country (id, name,iso_code) VALUES("1162", "Oman", "OM");
INSERT INTO contact_country (id, name,iso_code) VALUES("1163", "Pakistan", "PK");
INSERT INTO contact_country (id, name,iso_code) VALUES("1164", "Palau", "PW");
INSERT INTO contact_country (id, name,iso_code) VALUES("1165", "Palestinian Territory, Occupied", "PS");
INSERT INTO contact_country (id, name,iso_code) VALUES("1166", "Panama", "PA");
INSERT INTO contact_country (id, name,iso_code) VALUES("1167", "Papua New Guinea", "PG");
INSERT INTO contact_country (id, name,iso_code) VALUES("1168", "Paraguay", "PY");
INSERT INTO contact_country (id, name,iso_code) VALUES("1169", "Peru", "PE");
INSERT INTO contact_country (id, name,iso_code) VALUES("1170", "Philippines", "PH");
INSERT INTO contact_country (id, name,iso_code) VALUES("1171", "Pitcairn Islands", "PN");
INSERT INTO contact_country (id, name,iso_code) VALUES("1172", "Poland", "PL");
INSERT INTO contact_country (id, name,iso_code) VALUES("1173", "Portugal", "PT");
INSERT INTO contact_country (id, name,iso_code) VALUES("1174", "Puerto Rico", "PR");
INSERT INTO contact_country (id, name,iso_code) VALUES("1175", "Qatar", "QA");
INSERT INTO contact_country (id, name,iso_code) VALUES("1176", "Romania", "RO");
INSERT INTO contact_country (id, name,iso_code) VALUES("1177", "Russian Federation", "RU");
INSERT INTO contact_country (id, name,iso_code) VALUES("1178", "Rwanda", "RW");
INSERT INTO contact_country (id, name,iso_code) VALUES("1179", "Reunion", "RE");
INSERT INTO contact_country (id, name,iso_code) VALUES("1180", "Saint Helena", "SH");
INSERT INTO contact_country (id, name,iso_code) VALUES("1181", "Saint Kitts and Nevis", "KN");
INSERT INTO contact_country (id, name,iso_code) VALUES("1182", "Saint Lucia", "LC");
INSERT INTO contact_country (id, name,iso_code) VALUES("1183", "Saint Pierre and Miquelon", "PM");
INSERT INTO contact_country (id, name,iso_code) VALUES("1184", "Saint Vincent and the Grenadines", "VC");
INSERT INTO contact_country (id, name,iso_code) VALUES("1185", "Samoa", "WS");
INSERT INTO contact_country (id, name,iso_code) VALUES("1186", "San Marino", "SM");
INSERT INTO contact_country (id, name,iso_code) VALUES("1187", "Saudi Arabia", "SA");
INSERT INTO contact_country (id, name,iso_code) VALUES("1188", "Senegal", "SN");
INSERT INTO contact_country (id, name,iso_code) VALUES("1189", "Seychelles", "SC");
INSERT INTO contact_country (id, name,iso_code) VALUES("1190", "Sierra Leone", "SL");
INSERT INTO contact_country (id, name,iso_code) VALUES("1191", "Singapore", "SG");
INSERT INTO contact_country (id, name,iso_code) VALUES("1192", "Slovakia", NULL);
INSERT INTO contact_country (id, name,iso_code) VALUES("1193", "Slovenia", "SI");
INSERT INTO contact_country (id, name,iso_code) VALUES("1194", "Solomon Islands", "SB");
INSERT INTO contact_country (id, name,iso_code) VALUES("1195", "Somalia", "SO");
INSERT INTO contact_country (id, name,iso_code) VALUES("1196", "South Africa", "ZA");
INSERT INTO contact_country (id, name,iso_code) VALUES("1197", "South Georgia and the South Sandwich Islands", "GS");
INSERT INTO contact_country (id, name,iso_code) VALUES("1198", "Spain", "ES");
INSERT INTO contact_country (id, name,iso_code) VALUES("1199", "Sri Lanka", "LK");
INSERT INTO contact_country (id, name,iso_code) VALUES("1200", "Sudan", "SD");
INSERT INTO contact_country (id, name,iso_code) VALUES("1201", "Suriname", "SR");
INSERT INTO contact_country (id, name,iso_code) VALUES("1202", "Svalbard", NULL);
INSERT INTO contact_country (id, name,iso_code) VALUES("1203", "Swaziland", "SZ");
INSERT INTO contact_country (id, name,iso_code) VALUES("1204", "Sweden", "SE");
INSERT INTO contact_country (id, name,iso_code) VALUES("1205", "Switzerland", "CH");
INSERT INTO contact_country (id, name,iso_code) VALUES("1206", "Syria", "SY");
INSERT INTO contact_country (id, name,iso_code) VALUES("1207", "Sao Tome and Principe, Democratic Republic of", "ST");
INSERT INTO contact_country (id, name,iso_code) VALUES("1208", "Taiwan", "TW");
INSERT INTO contact_country (id, name,iso_code) VALUES("1209", "Tajikistan", "TJ");
INSERT INTO contact_country (id, name,iso_code) VALUES("1210", "Tanzania", "TZ");
INSERT INTO contact_country (id, name,iso_code) VALUES("1211", "Thailand", "TH");
INSERT INTO contact_country (id, name,iso_code) VALUES("1212", "Bahamas, The", "BS");
INSERT INTO contact_country (id, name,iso_code) VALUES("1213", "Gambia", "GM");
INSERT INTO contact_country (id, name,iso_code) VALUES("1214", "Togo", "TG");
INSERT INTO contact_country (id, name,iso_code) VALUES("1215", "Tokelau", "TK");
INSERT INTO contact_country (id, name,iso_code) VALUES("1216", "Tonga", "TO");
INSERT INTO contact_country (id, name,iso_code) VALUES("1217", "Trinidad and Tobago", "TT");
INSERT INTO contact_country (id, name,iso_code) VALUES("1218", "Tunisia", "TN");
INSERT INTO contact_country (id, name,iso_code) VALUES("1219", "Turkey", "TR");
INSERT INTO contact_country (id, name,iso_code) VALUES("1220", "Turkmenistan", "TM");
INSERT INTO contact_country (id, name,iso_code) VALUES("1221", "Turks and Caicos Islands", "TC");
INSERT INTO contact_country (id, name,iso_code) VALUES("1222", "Tuvalu", "TV");
INSERT INTO contact_country (id, name,iso_code) VALUES("1223", "Uganda", "UG");
INSERT INTO contact_country (id, name,iso_code) VALUES("1224", "Ukraine", "UA");
INSERT INTO contact_country (id, name,iso_code) VALUES("1225", "United Arab Emirates", "AE");
INSERT INTO contact_country (id, name,iso_code) VALUES("1226", "United Kingdom", "GB");
INSERT INTO contact_country (id, name,iso_code) VALUES("1227", "United States Minor Outlying Islands", "UM");
INSERT INTO contact_country (id, name,iso_code) VALUES("1228", "United States", "US");
INSERT INTO contact_country (id, name,iso_code) VALUES("1229", "Uruguay", "UY");
INSERT INTO contact_country (id, name,iso_code) VALUES("1230", "Uzbekistan", "UZ");
INSERT INTO contact_country (id, name,iso_code) VALUES("1231", "Vanuatu", "VU");
INSERT INTO contact_country (id, name,iso_code) VALUES("1232", "Venezuela", "VE");
INSERT INTO contact_country (id, name,iso_code) VALUES("1233", "Vietnam", "VN");
INSERT INTO contact_country (id, name,iso_code) VALUES("1234", "Virgin Islands, U.S.", "VI");
INSERT INTO contact_country (id, name,iso_code) VALUES("1235", "Wallis and Futuna", "WF");
INSERT INTO contact_country (id, name,iso_code) VALUES("1236", "Western Sahara", "EH");
INSERT INTO contact_country (id, name,iso_code) VALUES("1237", "Yemen", "YE");
INSERT INTO contact_country (id, name,iso_code) VALUES("1238", "Yugoslavia", "YU");
INSERT INTO contact_country (id, name,iso_code) VALUES("1239", "Zambia", "ZM");
INSERT INTO contact_country (id, name,iso_code) VALUES("1240", "Zimbabwe", "ZW");



#
# Insert data for table 'contact_state_province'
#

INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1000", "Alabama", "AL", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1001", "Alaska", "AK", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1002", "Arizona", "AZ", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1003", "Arkansas", "AR", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1004", "California", "CA", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1005", "Colorado", "CO", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1006", "Connecticut", "CT", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1007", "Delaware", "DE", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1008", "Florida", "FL", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1009", "Georgia", "GA", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1010", "Hawaii", "HI", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1011", "Idaho", "ID", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1012", "Illinois", "IL", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1013", "Indiana", "IN", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1014", "Iowa", "IA", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1015", "Kansas", "KS", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1016", "Kentucky", "KY", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1017", "Louisiana", "LA", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1018", "Maine", "ME", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1019", "Maryland", "MD", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1020", "Massachusetts", "MA", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1021", "Michigan", "MI", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1022", "Minnesota", "MN", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1023", "Mississippi", "MI", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1024", "Missouri", "MO", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1025", "Montana", "MT", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1026", "Nebraska", "NE", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1027", "Nevada", "NV", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1028", "New Hampshire", "NV", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1029", "New Jersey", "NJ", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1030", "New Mexico", "NM", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1031", "New York", "NY", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1032", "North Carolina", "NC", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1033", "North Dakota", "ND", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1034", "Ohio", "OH", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1035", "Oklahoma", "OK", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1036", "Oregon", "OR", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1037", "Pennsylvania", "PA", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1038", "Rhode Island", "RI", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1039", "South Carolina", "SC", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1040", "South Dakota", "SD", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1041", "Tennessee", "TN", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1042", "Texas", "TX", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1043", "Utah", "UT", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1044", "Vermont", "VT", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1045", "Virginia", "VA", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1046", "Washington", "WA", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1047", "West Virginia", "WV", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1048", "Wisconsin", "WI", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1049", "Wyoming", "WY", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1050", "District of Columbia", "DC", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1051", "APO", "XX", 1228);
-- American Territories
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1052", "American Samoa", "AS", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1053", "Guam", "GU", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1054", "Marshall Islands", "MH", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1055", "Northern Mariana Islands", "MP", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1056", "Puerto Rico", "PR", 1228);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1057", "Virgin Islands", "VI", 1228);
-- Canadian Provinces
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1058", "Alberta", "AB", 1039);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1059", "British Columbia", "BC", 1039);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1060", "Manitoba", "MB", 1039);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1061", "New Brunswick", "NB", 1039);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1062", "Newfoundland", "NL", 1039);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1063", "Northwest Territories", "NT", 1039);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1064", "Nova Scotia", "NS", 1039);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1065", "Nunavut", "NU", 1039);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1066", "Ontario", "ON", 1039);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1067", "Prince Edward Island", "PE", 1039);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1068", "Quebec", "QC", 1039);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1069", "Saskatchewan", "SK", 1039);
INSERT INTO contact_state_province (id, name, abbreviation, countryid) VALUES("1070", "Yukon Territory", "YT", 1039);

