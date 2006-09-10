/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | copyright CiviCRM LLC (c) 2004-2006                                  |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

function getHelpText(input, evt) 
{

    if (input.value.length == 0) {
        return;
    }
    
    /*allow backspace to work in IE*/
    if (typeof input.selectionStart == 'undefined' && evt.keyCode == 8) { input.value = input.value.substr(0,input.value.length-1); }

    /*Ignore the following keystrokes*/
    switch (evt.keyCode) {
        case 37: //left arrow
        case 39: //right arrow
        case 33: //page up  
        case 34: //page down  
        case 36: //home  
        case 35: //end
        case 13: //enter
        case 9: //tab
        case 27: //esc
        case 16: //shift  
        case 17: //ctrl  
        case 18: //alt  
        case 20: //caps lock
        case 8: //backspace  
        case 46: //delete 
        case 38: //up arrow 
        case 40: //down arrow
        return;
        break;
    }

    /*Remember the current length to allow selection*/
    CompletionHandler.lastLength = input.value.length;
      
    /*Create the remote client*/
    var a = new crm_contact_server_uf (CompletionHandler);

    /*Set a timeout for responses which take too long*/
    a.timeout = 3000;
    
    /*Ignore timeouts*/
    a.clientErrorFunc = function(e) {
        if ( e.code == 1003 ) {
            /* Ignore...*/
        } else {
            alert(e);
        }
    }

    /*Call the remote method*/
    a.gethelptext(input.value);
}

/*Callback handler*/
var CompletionHandler = {

    lastLength: 0,
    
    /*Callback method*/
    gethelptext: function(result) {
	
	var input = document.getElementById('help_post');
	input.value = result;

        try {
            input.setSelectionRange(this.lastLength, input.value.length);
        } catch(e) {
        }
    }
    
}
