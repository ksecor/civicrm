
/*******************************************************
*
* form_components.sql
*
*    This script creates the schema for storage of
*	 forms, form groups, and form_elements
*    (used by contact management system).
*
*	 Initially used for dynamic properties only. May
*	 be extended for use with core CRM forms.
*
*******************************************************/

CONNECT crm;

/*******************************************************
*
* crm_forms
* Meta info for forms
*
*******************************************************/
CREATE TABLE IF NOT EXISTS crm_forms (

	fid			INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'form id',
	title		VARCHAR(255) NOT NULL default '' COMMENT 'title for display',
	description	VARCHAR(255),
	help		TEXT NOT NULL default '',
	is_deleted	BOOLEAN NOT NULL DEFAULT 0 COMMENT 'is this entry deleted ?',
	PRIMARY KEY (fid)

) ENGINE=InnoDB COMMENT='Meta info for forms (for dynamic properties)';

/*******************************************************
*
* crm_form_groups
* Meta info for logical/visual grouping of form fields
*
*******************************************************/
CREATE TABLE IF NOT EXISTS crm_form_groups (

	fgid		INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'form group id',
	title		VARCHAR(255) NOT NULL default '' COMMENT 'title (legend) for display',
	description	VARCHAR(255),
	validate	VARCHAR(255) COMMENT 'optional input validation function (hook) for this group of fields'
	help		TEXT NOT NULL default '',
	is_deleted	BOOLEAN NOT NULL DEFAULT 0 COMMENT 'is this entry deleted ?',
	PRIMARY KEY (fgid)

) ENGINE=InnoDB COMMENT='Meta info for logical/visual grouping of form fields';

/*******************************************************
*
* crm_form_fields
* Defines form fields and their attributes
*
*******************************************************/
CREATE TABLE IF NOT EXISTS crm_form_fields (

	ffid		INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'field id',
	field_name	VARCHAR(255) NOT NULL default '' COMMENT 'name assigned to HTML form element'
	label		VARCHAR(255) NOT NULL default '' COMMENT 'field label for display',
	field_type ENUM('text_string','text_int','text_money','text_float','date','select','radio','checkbox(_int?_text?)','memo') COMMENT 'type of 
contact',
-- Maybe have data_type AND field_type ??
	validate	VARCHAR(255) COMMENT 'optional input validation function (hook) for this group of fields'
	help		TEXT NOT NULL default '',
	extends		VARCHAR(255) DEFAULT 'contact' COMMENT 'Which table/object does this property extend',
	is_deleted	BOOLEAN NOT NULL DEFAULT 0 COMMENT 'is this entry deleted ?',
	PRIMARY KEY (fgid)

) ENGINE=InnoDB COMMENT='Defines form fields and their attributes';


