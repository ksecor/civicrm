--
-- This is a temporary table for testing purposes only.
--


-- connect to the db
-- currently lets just use drupal.
CONNECT drupal;

/*******************************************************
*
* CREATE TABLES
*
*******************************************************/

/*******************************************************
*
* cms2_contact
*
*******************************************************/
CREATE TABLE IF NOT EXISTS cms2_contact(

	id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'contact id',

	first_name VARCHAR(255) COMMENT 'First Name',
	last_name VARCHAR(255) COMMENT 'Last Name',

	address_line_1 VARCHAR(255) COMMENT 'Address Line 1',
	address_line_2 VARCHAR(255) COMMENT 'Address Line 2',
	city VARCHAR(255) COMMENT 'City',
	state VARCHAR(255) COMMENT 'State',
	zipcode VARCHAR(255) COMMENT 'Zipcode',

	email VARCHAR(255) COMMENT 'Email',
	tel_home VARCHAR(255) COMMENT 'Home Tel No',
	tel_work VARCHAR(255) COMMENT 'Work Tel No',
	tel_cell VARCHAR(255) COMMENT 'Cellular Tel No',

	ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'time it was added/updated',

	PRIMARY KEY (id)

) ENGINE=InnoDB COMMENT='Manage Contacts';

INSERT INTO `cms2_contact` VALUES
	(1,'','','','','','','','','','','','2004-11-05 02:06:59'),
	(2,'Yahesh','Bhatia','','','','','','','','','','2004-11-05 02:10:41'),
	(3,'Yashesh','Bhatia','a1','a2','c1','s1','z1','e1','','','','2004-11-05 02:11:21'),
	(4,'Yashesh','Bhatia','a1','a2','c1','s1','z1','yasheshb@yahoo.com','t1','t2','t3','2004-11-05 02:14:10'),
	(5,'Don','Lobo','a1','a2','c1','s1','z1','lobo@yahoo.com','t1','t2','t3','2004-11-05 15:31:30'),
	(6,'Stephen','Lobo','a1','a2','c1','s1','z1','e1','t1','t2','t3','2004-11-05 15:32:00'),
	(7,'Deval','Sanghavi','a1','a2','c1','s1','z1','deval@dasra.org','','','','2004-11-05 15:32:40'),
	(8,'Milan','Bhatia','a1','a2','c1','s1','z1','milan.bhatia@gmail.com','t1','t2','t3','2004-11-05 15:33:23'),
	(9,'Neha','Bhatia','a1','a2','c1','c2','z1','drnehabhatia@gmail.com','t1','t2','t3','2004-11-05 15:34:05'),
	(10,'Neha','Gupta','a1','a2','mumbai','','','guptanv_2001@yahoo.co.in','','','','2004-11-05 15:34:43'),
	(11,'Vishal','Paleja','','','mumbai','','','vishalpaleja@yahoo.com','','','','2004-11-05 15:35:29'),
	(12,'Brian','Stormont','','','providence','ri','','mosttornbrain@yahoo.com','','','','2004-11-05 15:36:00'),
	(13,'Neha','Bhatia','a1','a2','mumbai','','','jarin72@yahoo.com','','','','2004-11-05 23:54:35'),
	(14,'yab','bhatia','a1','a2','c1','s1','z1','','','','','2004-11-09 19:30:17');

