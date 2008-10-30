<?php
require_once('AuditConfig.php');

class Audit
{
	private $auditConfig;
	
	public function __construct($xmlString, $confFilename)
	{
		$this->xmlString = $xmlString;
		$this->auditConfig = new AuditConfig($confFilename);
	}
		
	public function getActivities()
	{
		$retval = array();
	
		/*
		 * Loop through the activities in the file and add them to the appropriate region array.
		 */
		$doc = new DOMDocument();
		if ($doc->loadXML($this->xmlString))
		{
			$regionList = $this->auditConfig->getRegions();
			
			$activityindex = 0;
			$activityList = $doc->getElementsByTagName("Activity");
			foreach($activityList as $activity)
			{
				$retval[$activityindex] = array();
				
				$completed = false;
				$fieldindex = 1;
				$fields = $activity->getElementsByTagName("Field");
				foreach($fields as $field)
				{
					$datatype_elements = $field->getElementsByTagName("Type");
					$datatype = $datatype_elements->item(0)->nodeValue;
					
					$label_elements = $field->getElementsByTagName("Label");
					$label = $label_elements->item(0)->nodeValue;
				
					$value_elements = $field->getElementsByTagName("Value");
					$value = $value_elements->item(0)->nodeValue;
	
					if ($label == $this->auditConfig->getCompletionLabel() && $value == $this->auditConfig->getCompletionValue())
					{
						$completed = true;
					}
					
					foreach($regionList as $region)
					{
						if ($this->auditConfig->includeInRegion($label, $region))
						{
							$retval[$activityindex][$region][$fieldindex] = array();
							$retval[$activityindex][$region][$fieldindex]['label'] = $label;
							$retval[$activityindex][$region][$fieldindex]['datatype'] = $datatype;
							$retval[$activityindex][$region][$fieldindex]['value'] = $value;
							if ($datatype == 'Date')
							{
								$retval[$activityindex][$region][$fieldindex]['includeTime'] = $this->auditConfig->includeTime($label, $region);
							}
						}
					}
	
					$fieldindex++;
				}
	
				// Now sort the fields based on the order in the config file.
				foreach($regionList as $region)
				{
					$this->auditConfig->sort($retval[$activityindex][$region], $region);
				}				
				
				$retval[$activityindex]['completed'] = $completed;
	
				$retval[$activityindex]['editurl'] = $activity->getElementsByTagName("EditURL")->item(0)->nodeValue;
							
				$activityindex++;
			}
		}		
            
		return $retval;
	}

    static function run( $xmlString ) {
        $audit = new Audit( $xmlString,
                            'audit.conf.xml' );
        $activities = $audit->getActivities();

        $template = CRM_Core_Smarty::singleton( );
        $template->assign_by_ref( 'activities', $activities );

        $contents = $template->fetch( 'CRM/Case/Audit/Audit.tpl' );
        echo $contents;
    }
}

// Audit::run( file_get_contents( 'CaseReport.xml' ) );