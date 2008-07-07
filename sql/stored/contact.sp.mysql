-- Get name and primary address / email / phone / im
-- details for a contact
CREATE PROCEDURE contactDetails( IN cid INT, OUT procFailed INT )
BEGIN
	DECLATE contactType, selectString, fromString STRING;
	DECLARE curContactType CURSOR FOR
	SELECT contact_type
	  FROM civicrm_contact
	 WHERE id = cid;
	DECLARE CONTINUE HANDLER FOR NOT FOUND
	  SET procFailed = 1;

        OPEN curContactType;
        FETCH curContactType INTO contactType;

	selectString = 'SELECT display_name, sort_name ';
	fromString   = 'civicrm_contact ';
	IF contact_type = 'Individual' THEN
		selectString .= 
	ELSE IF contact_type = 'Household' THEN
	ELSE IF contact_type = 'Organization' THEN
	END IF;
	SELECT id, sort_name, display_name
	  FROM civicrm_contact
	 WHERE id = cid;
END; //
