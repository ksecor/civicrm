



DROP DATABASE IF EXISTS crm;

CREATE DATABASE crm DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
use crm;

/*******************************************************
*
* CREATE TABLES
*
*******************************************************/

/*******************************************************
*
* crm_country
*
*******************************************************/
DROP TABLE IF EXISTS crm_country;
CREATE TABLE crm_country (


     id int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Country Id',
     name varchar(64)    COMMENT 'Country Name',
     iso_code char(2)    COMMENT 'ISO Code',
     country_code varchar(4)    COMMENT 'National prefix to be used when dialing TO this country.',
     idd_prefix varchar(4)    COMMENT 'International direct dialing prefix from within the country TO another country',
     ndd_prefix varchar(4)    COMMENT 'Access prefix to call within a country to a different area' 
,
    PRIMARY KEY ( id )
 
 
 
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;


/*******************************************************
*
* crm_state_province
*
*******************************************************/
DROP TABLE IF EXISTS crm_state_province;
CREATE TABLE crm_state_province (


     id int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'State / Province ID',
     name varchar(64)    COMMENT 'Name of State / Province',
     abbreviation varchar(4)    COMMENT '2-4 Character Abbreviation of State / Province',
     country_id int unsigned NOT NULL   COMMENT 'ID of Country that State / Province belong' 
,
    PRIMARY KEY ( id )
 
 
,
     FOREIGN KEY (country_id) REFERENCES crm_country(id)
  
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;


/*******************************************************
*
* crm_county
*
*******************************************************/
DROP TABLE IF EXISTS crm_county;
CREATE TABLE crm_county (


     id int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'County ID',
     name varchar(64)    COMMENT 'Name of County',
     abbreviation varchar(4)    COMMENT '2-4 Character Abbreviation of County',
     state_province_id int unsigned NOT NULL   COMMENT 'ID of State / Province that County belongs' 
,
    PRIMARY KEY ( id )
 
 
,
     FOREIGN KEY (state_province_id) REFERENCES crm_state_province(id)
  
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;


/*******************************************************
*
* crm_domain
*
* Top-level hierarchy to support multi-org/domain installations. Define domains for multi-org installs, else all contacts belong to one domain.
*
*******************************************************/
DROP TABLE IF EXISTS crm_domain;
CREATE TABLE crm_domain (


     id int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Domain ID',
     name varchar(64)    COMMENT 'Name of Domain / Organization',
     description varchar(255)    COMMENT 'Description of Domain.' 
,
    PRIMARY KEY ( id )
 
 
 
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;


/*******************************************************
*
* crm_mobile_provider
*
* Mobile Provider catalogue.
*
*******************************************************/
DROP TABLE IF EXISTS crm_mobile_provider;
CREATE TABLE crm_mobile_provider (


     id int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Mobile Provider ID',
     name varchar(64)    COMMENT 'Name of Mobile Provider.' 
,
    PRIMARY KEY ( id )
 
 
 
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;


/*******************************************************
*
* crm_geo_coord
*
* Geo code coordinate system info.
*
*******************************************************/
DROP TABLE IF EXISTS crm_geo_coord;
CREATE TABLE crm_geo_coord (


     id int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Geo Coord ID',
     coord_type enum('LatLong', 'Projected')    COMMENT 'Projected or unprojected coordinates - projected coordinates (e.g. UTM) may be treated as cartesian by some modules.',
     coord_units enum('Degree', 'Grad', 'Radian', 'Foot', 'Meter')    COMMENT 'If the coord_type is LATLONG, indicate the unit of angular measure: Degree|Grad|Radian; If the coord_type is Projected, indicate unit of distance measure: Foot|Meter.',
     coord_ogc_wkt_string text    COMMENT 'Coordinate sys description in Open GIS Consortium WKT (well known text) format - see http://www.opengeospatial.org/docs/01-009.pdf; this is provided for the convenience of the user or third party modules.' 
,
    PRIMARY KEY ( id )
 
 
 
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;


/*******************************************************
*
* crm_im_provider
*
* IM Provider catalogue.
*
*******************************************************/
DROP TABLE IF EXISTS crm_im_provider;
CREATE TABLE crm_im_provider (


     id int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'IM Provider ID',
     name varchar(64)    COMMENT 'Name of IM Provider.' 
,
    PRIMARY KEY ( id )
 
 
 
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;


/*******************************************************
*
* crm_note
*
* Notes can be linked to any object in the application.
*
*******************************************************/
DROP TABLE IF EXISTS crm_note;
CREATE TABLE crm_note (


     id int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Note ID',
     table_name varchar(64) NOT NULL   COMMENT 'Name of table where item being referenced is stored.',
     table_id int unsigned NOT NULL   COMMENT 'Foreign key to the referenced item.',
     note text    COMMENT 'Note and/or Comment.' 
,
    PRIMARY KEY ( id )
 
 
 
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;


/*******************************************************
*
* crm_category
*
* Provides support for flat or hierarchical classification of various types of entities (contacts, groups, actions...).
*
*******************************************************/
DROP TABLE IF EXISTS crm_category;
CREATE TABLE crm_category (


     id int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Category ID',
     domain_id int unsigned NOT NULL   COMMENT 'Which Domain owns this contact',
     name varchar(64)    COMMENT 'Name of Category.',
     description varchar(255)    COMMENT 'Optional verbose description of the category.',
     parent_id int unsigned    COMMENT 'Optional parent id for this category.' 
,
    PRIMARY KEY ( id )
 
 
,
     FOREIGN KEY (domain_id) REFERENCES crm_domain(id)
  
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;


/*******************************************************
*
* crm_relationship_type
*
* Relationship types s/b structured with contact_a as the 'subject/child' contact and contact_b as the 'object/parent' contact (e.g. Individual A is Employee of Org B).
*
*******************************************************/
DROP TABLE IF EXISTS crm_relationship_type;
CREATE TABLE crm_relationship_type (


     id int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Category ID',
     domain_id int unsigned NOT NULL   COMMENT 'Which Domain owns this contact',
     name_a_b varchar(64)    COMMENT 'name/label for relationship of contact_a to contact_b.',
     name_b_a varchar(64)    COMMENT 'Optional name/label for relationship of contact_b to contact_a.',
     description varchar(255)    COMMENT 'Optional verbose description of the category.',
     contact_type_a enum('Individual', 'Organization', 'Household')    COMMENT 'If defined, contact_a in a relationship of this type must be a specific contact_type.',
     contact_type_b enum('Individual', 'Organization', 'Household')    COMMENT 'If defined, contact_b in a relationship of this type must be a specific contact_type.',
     is_reserved boolean    COMMENT 'Is this location type a predefined system location?' 
,
    PRIMARY KEY ( id )
 
 
,
     FOREIGN KEY (domain_id) REFERENCES crm_domain(id)
  
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;


/*******************************************************
*
* crm_location_type
*
*******************************************************/
DROP TABLE IF EXISTS crm_location_type;
CREATE TABLE crm_location_type (


     id int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Location Type ID',
     domain_id int unsigned NOT NULL   COMMENT 'Which Domain owns this location type.',
     name varchar(64)    COMMENT 'Location Type Name.',
     description varchar(255)    COMMENT 'Location Type Description.',
     is_reserved boolean    COMMENT 'Is this location type a predefined system location?' 
,
    PRIMARY KEY ( id )
 
 
,
     FOREIGN KEY (domain_id) REFERENCES crm_domain(id)
  
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;


/*******************************************************
*
* crm_contact
*
* Three types of contacts are defined: Individual, Organization and Household. Contact objects are defined by a crm_contact record plus a related crm_contact_type record.
*
*******************************************************/
DROP TABLE IF EXISTS crm_contact;
CREATE TABLE crm_contact (


     id int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Unique Contact ID',
     domain_id int unsigned NOT NULL   COMMENT 'Which Domain owns this contact',
     contact_type enum('Individual', 'Organization', 'Household')    COMMENT 'Type of Contact.',
     legal_id varchar(32)    COMMENT 'May be used for SSN, EIN/TIN, Household ID (census) or other applicable unique legal/government ID.',
     external_id varchar(32)    COMMENT 'Unique trusted external ID (generally from a legacy app/datasource). Particularly useful for deduping operations.',
     sort_name varchar(64)    COMMENT 'Name used for sorting different contact types',
     home_URL varchar(128)    COMMENT 'optional "home page" URL for this contact.',
     image_URL varchar(128)    COMMENT 'optional URL for preferred image (photo, logo, etc.) to display for this contact.',
     source varchar(255)    COMMENT 'where domain_id contact come from, e.g. import, donate module insert...',
     preferred_communication_method enum('Phone', 'Email', 'Post')    COMMENT 'What is the preferred mode of communication.',
     do_not_phone boolean   DEFAULT 'false' ,
     do_not_email boolean   DEFAULT 'false' ,
     do_not_mail boolean   DEFAULT 'false' ,
     hash int unsigned NOT NULL   COMMENT 'Key for validating requests related to this contact.' 
,
    PRIMARY KEY ( id )
 
,
 INDEX index_sort_name(sort_name)
  
,
     FOREIGN KEY (domain_id) REFERENCES crm_domain(id)
  
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;


/*******************************************************
*
* crm_individual
*
* Define individual specific properties
*
*******************************************************/
DROP TABLE IF EXISTS crm_individual;
CREATE TABLE crm_individual (


     id int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Unique Individual ID',
     contact_id int unsigned NOT NULL   COMMENT 'FK to Contact ID',
     first_name varchar(64)    COMMENT 'First Name.',
     middle_name varchar(64)    COMMENT 'Middle Name.',
     last_name varchar(64)    COMMENT 'Last Name.',
     prefix varchar(64)    COMMENT 'Prefix to Name.',
     suffix varchar(64)    COMMENT 'Suffix to Name.',
     display_name varchar(128)    COMMENT 'Formatted name representing preferred format for display/print/other output.',
     greeting_type enum('Formal', 'Informal', 'Honorific', 'Custom', 'Other')    COMMENT 'Preferred greeting format.',
     custom_greeting varchar(128)    COMMENT 'Custom greeting message.',
     job_title varchar(64)    ,
     gender enum('Female', 'Male', 'Transgender')    ,
     birth_date date    ,
     is_deceased boolean   DEFAULT 'false' ,
     phone_to_household_id int unsigned    COMMENT 'OPTIONAL FK to crm_contact_household record. If NOT NULL, direct phone communications to household rather than individual location.',
     email_to_household_id int unsigned    COMMENT 'OPTIONAL FK to crm_contact_household record. If NOT NULL, direct phone communications to household rather than individual location.',
     mail_to_household_id int unsigned    COMMENT 'OPTIONAL FK to crm_contact_household record. If NOT NULL, direct mail communications to household rather than individual location.' 
,
    PRIMARY KEY ( id )
 
 
,
     FOREIGN KEY (contact_id) REFERENCES crm_contact(id)
  
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;


/*******************************************************
*
* crm_household
*
* Define household specific properties
*
*******************************************************/
DROP TABLE IF EXISTS crm_household;
CREATE TABLE crm_household (


     id int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Unique Household ID',
     contact_id int unsigned NOT NULL   COMMENT 'FK to Contact ID',
     household_name varchar(64)    COMMENT 'Household Name.',
     nick_name varchar(64)    COMMENT 'Nick Name.',
     primary_contact_id int unsigned NOT NULL   COMMENT 'Optional FK to Primary Contact for this household.' 
,
    PRIMARY KEY ( id )
 
 
,
     FOREIGN KEY (contact_id) REFERENCES crm_contact(id)
,
     FOREIGN KEY (primary_contact_id) REFERENCES crm_contact(id)
  
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;


/*******************************************************
*
* crm_organization
*
* Define organization specific properties
*
*******************************************************/
DROP TABLE IF EXISTS crm_organization;
CREATE TABLE crm_organization (


     id int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Unique Organization ID',
     contact_id int unsigned NOT NULL   COMMENT 'FK to Contact ID',
     organization_name varchar(64)    COMMENT 'Organization Name.',
     legal_name varchar(64)    COMMENT 'Legal Name.',
     nick_name varchar(64)    COMMENT 'Nick Name.',
     sic_code varchar(8)    COMMENT 'Standard Industry Classification Code.',
     primary_contact_id int unsigned NOT NULL   COMMENT 'Optional FK to Primary Contact for this organization.' 
,
    PRIMARY KEY ( id )
 
 
,
     FOREIGN KEY (contact_id) REFERENCES crm_contact(id)
,
     FOREIGN KEY (primary_contact_id) REFERENCES crm_contact(id)
  
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;


/*******************************************************
*
* crm_location
*
* Define location specific properties
*
*******************************************************/
DROP TABLE IF EXISTS crm_location;
CREATE TABLE crm_location (


     id int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Unique Location ID',
     contact_id int unsigned NOT NULL   COMMENT 'FK to Contact ID',
     location_type_id int unsigned NOT NULL   COMMENT 'FK to Location Type ID',
     is_primary boolean   DEFAULT 'false' COMMENT 'Is this the primary location for the contact. (allow only ONE primary location / contact.)' 
,
    PRIMARY KEY ( id )
 
,
 INDEX index_contact_location_type(contact_id)
  
,
     FOREIGN KEY (contact_id) REFERENCES crm_contact(id)
,
     FOREIGN KEY (location_type_id) REFERENCES crm_location_type(id)
  
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;


/*******************************************************
*
* crm_address
*
* Stores the physical street / mailing address. This format should be capable of storing ALL international addresses.
*
*******************************************************/
DROP TABLE IF EXISTS crm_address;
CREATE TABLE crm_address (


     id int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Unique Address ID',
     location_id int unsigned NOT NULL   COMMENT 'Which Location does this address belong to.',
     street_address varchar(96)    COMMENT 'Concatenation of all routable street address components (prefix, street number, street name, suffix, unit number OR P.O. Box. Apps should be able to determine physical location with this data (for mapping, mail delivery, etc.).',
     street_number int    COMMENT 'Numeric portion of address number on the street, e.g. For 112A Main St, the street_number = 112.',
     street_number_suffix varchar(8)    COMMENT 'Non-numeric portion of address number on the street, e.g. For 112A Main St, the street_number_suffix = A',
     street_number_predirectional varchar(8)    COMMENT 'Directional prefix, e.g. SE Main St, SE is the prefix.',
     street_name varchar(64)    COMMENT 'Actual street name, excluding St, Dr, Rd, Ave, e.g. For 112 Main St, the street_name = Main.',
     street_type varchar(8)    COMMENT 'St, Rd, Dr, etc.',
     street_number_postdirectional varchar(8)    COMMENT 'Directional prefix, e.g. Main St S, S is the suffix.',
     street_unit varchar(16)    COMMENT 'Secondary unit designator, e.g. Apt 3 or Unit # 14, or Bldg 1200',
     supplemental_address_1 varchar(96)    COMMENT 'Supplemental Address Information, Line 1',
     supplemental_address_2 varchar(96)    COMMENT 'Supplemental Address Information, Line 2',
     supplemental_address_3 varchar(96)    COMMENT 'Supplemental Address Information, Line 3',
     city varchar(64)    COMMENT 'City, Town or Village Name.',
     county_id int unsigned NOT NULL   COMMENT 'Which County does this address belong to.',
     state_province_id int unsigned NOT NULL   COMMENT 'Which State_Province does this address belong to.',
     postal_code varchar(12)    COMMENT 'Store both US (zip5) AND international postal codes. App is responsible for country/region appropriate validation.',
     postal_code_suffix varchar(12)    COMMENT 'Store the suffix, like the +4 part in the USPS system.',
     usps_adc varchar(32)    COMMENT 'USPS Bulk mailing code.',
     country_id int unsigned NOT NULL   COMMENT 'Which Country does this address belong to.',
     geo_coord_id int unsigned NOT NULL   COMMENT 'Which Geo_Coord does this address belong to.',
     geo_code_1 float    COMMENT 'Latitude or UTM (Universal Transverse Mercator Grid) Northing.',
     geo_code_2 float    COMMENT 'Longitude or UTM (Universal Transverse Mercator Grid) Easting.',
     timezone varchar(8)    COMMENT 'Timezone expressed as a UTC offset - e.g. United States CST would be written as "UTC-6".',
     address_nite varchar(255)    COMMENT 'Optional misc info (e.g. delivery instructions) for this address.' 
,
    PRIMARY KEY ( id )
 
 
,
     FOREIGN KEY (location_id) REFERENCES crm_location(id)
,
     FOREIGN KEY (county_id) REFERENCES crm_county(id)
,
     FOREIGN KEY (state_province_id) REFERENCES crm_state_province(id)
,
     FOREIGN KEY (country_id) REFERENCES crm_country(id)
,
     FOREIGN KEY (geo_coord_id) REFERENCES crm_geo_coord(id)
  
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;


/*******************************************************
*
* crm_phone
*
* Phone information for a specific location.
*
*******************************************************/
DROP TABLE IF EXISTS crm_phone;
CREATE TABLE crm_phone (


     id int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Unique Phone ID',
     location_id int unsigned NOT NULL   COMMENT 'Which Location does this phone belong to.',
     phone varchar(16)    COMMENT 'Complete phone number.',
     phone_type enum('Phone', 'Mobile', 'Fax', 'Pager')    COMMENT 'What type of telecom device is this.',
     is_primary boolean   DEFAULT 'false' COMMENT 'Is this the primary phone for this contact and location.',
     mobile_provider_id int unsigned    COMMENT 'Which Mobile Provider does this phone belong to.' 
,
    PRIMARY KEY ( id )
 
 
,
     FOREIGN KEY (location_id) REFERENCES crm_location(id)
,
     FOREIGN KEY (mobile_provider_id) REFERENCES crm_mobile_provider(id)
  
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;


/*******************************************************
*
* crm_email
*
* Email information for a specific location.
*
*******************************************************/
DROP TABLE IF EXISTS crm_email;
CREATE TABLE crm_email (


     id int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Unique Email ID',
     location_id int unsigned NOT NULL   COMMENT 'Which Location does this email belong to.',
     email varchar(64)    COMMENT 'Email address',
     is_primary boolean   DEFAULT 'false' COMMENT 'Is this the primary email for this contact and location.' 
,
    PRIMARY KEY ( id )
 
 
,
     FOREIGN KEY (location_id) REFERENCES crm_location(id)
  
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;


/*******************************************************
*
* crm_im
*
* IM information for a specific location.
*
*******************************************************/
DROP TABLE IF EXISTS crm_im;
CREATE TABLE crm_im (


     id int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Unique IM ID',
     location_id int unsigned NOT NULL   COMMENT 'Which Location does this IM identifier belong to.',
     im_screenname varchar(64)    COMMENT 'IM screen name',
     im_provider_id int unsigned    COMMENT 'Which IM Provider does this screen name belong to.',
     is_primary boolean   DEFAULT 'false' COMMENT 'Is this the primary IM for this contact and location.' 
,
    PRIMARY KEY ( id )
 
 
,
     FOREIGN KEY (location_id) REFERENCES crm_location(id)
,
     FOREIGN KEY (im_provider_id) REFERENCES crm_im_provider(id)
  
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;


 