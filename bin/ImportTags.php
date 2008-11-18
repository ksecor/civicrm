<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright Tech To The People http:tttp.eu (c) 2008                 |
 +--------------------------------------------------------------------+
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

require_once ('bin/cli.php');
require_once 'CRM/Core/BAO/Tag.php';

/$ this
class tagsImporter extends civicrm_cli {

    function __construct() {
       parent::__construct ();
       if (sizeof($this->args) != 1) {
	  die ("you need to profide a csv file (1st column parent name, 2nd tag name");
       }
       $this->file = $this->args [0];
        $this->tags = array_flip(CRM_Core_PseudoConstant::tag());
    }

//format expected: parent name, tag
function run() {
	$row = 1;
	$handle = fopen($this->file, "r");
	//if you have a header with the name of the fields on the first line, uncomment
	//$header = fgetcsv($handle, 1000, ",");
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	    $num = count($data);
	    $row++;
	    $params = $this->convertLine ($data);
	    $this->addTag($params);
	    }
	fclose($handle);
    return;
}

function addTag ($param) {
   if (array_key_exists($param['name'],$this->tags)) {
       echo "\n- exists already: ".$param['name'];
       return;
   }
   $key=array('tag'=>'');
   if ($param['parent']) {
	if (array_key_exists($param['parent'],$this->tags)) 
	  $param['parent_id']=$this->tags[$param['parent']];
	else
	  $param['parent_id']=$this->addTag(array(parent=>'',name=>$param['parent']));
       $tag=CRM_Core_BAO_Tag::add($param,$key);
       echo "\n".$tag->id.": create ".$param['name'] ." below ". $param['parent'];
   } else {
     $tag=CRM_Core_BAO_Tag::add($param,$key);
     echo "\n".$tag->id.": create ".$param['name'] ." (root)";
   }
   $this->tags[$param['name']]=$tag->id;
   return $tag->id;
}

/* format the csv line so it returns the params as expected 
    first column = parent tag name
    second column = name of the tag
*/
function convertLine ($data) {
   $params ['parent'] = $data[0];
   $params ['name'] = $data[1];
   return $params;
}

}

$tagsImporter = new tagsImporter ();
$tagsImporter->run();
echo "\n";
?>
