<?php
// $Id: mailresponder.php,v 1.2 2004/11/16 14:57:57 harryf Exp $
/***
* A responder that uses PEAR::Mail_IMAP for fetching data from a
* mail server
* Requires: PEAR::Mail_IMAP and PHP imap extension installed
* Modify the mailUrl variable in the Mail class below
*
* @TODO: secure the responder with HTTP Auth
*/

/**
* This is a remote script to call from Javascript
*/
// IE's XMLHttpRequest caching...
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header( "Cache-Control: no-cache, must-revalidate" ); 
header( "Pragma: no-cache" );

require_once '../JPSpan.php';
require_once JPSPAN . 'Listener.php';
require_once JPSPAN . 'Serializer.php';
require_once JPSPAN . 'Types.php';

// PEAR Mail_IMAP
require_once 'Mail/IMAP.php';

// Sadly required for Mail_IMAP
error_reporting (E_ALL ^ E_NOTICE);

class Mail {
    /**
    * Modify this
    */
    var $mailUrl = 'imap://user:pass@mail.someserver.net:143/INBOX';

    function getMessageList() {
        $conn =& new Mail_IMAP();
        $status = & $conn->connect($this->mailUrl);
        if (PEAR::isError($status)) {
            $e = new JPSpan_Error();
            $e->setError('ConnectError',$status->getMessage());
            return $e;
        }
        $msgcount = $conn->messageCount();
        $messages = array();
        for ($mid = 1; $mid <= $msgcount; $mid++) {
            $pid = $conn->getDefaultPid($mid);
            $conn->getHeaders($mid, $pid);
            $message = array();
            $message['mid'] = $mid;
            $message['pid'] = $pid;
            if (!isset($conn->header[$mid]['subject']) ||
                empty($conn->header[$mid]['subject'])) {
                $message['subject'] = 'No subject';
        	} else {
                $message['subject'] = trim($conn->header[$mid]['subject']);
            }
            if ( isset($conn->header[$mid]['from_personal'][0]) &&
                    !empty($conn->header[$mid]['from_personal'][0]) ) {
                $message['from'] = $conn->header[$mid]['from_personal'][0];
            } else {
                $message['from'] = $conn->header[$mid]['from'][0];
            }
            $message['date'] = date('D, M d, Y h:i:s', $conn->header[$mid]['udate']);
            $conn->unsetHeaders($mid);
            $messages[] = $message;
        }
        $conn->close();
        return $messages;
    }
    function getMessage($mid,$pid) {
        $conn =& new Mail_IMAP();
        $status = & $conn->connect($this->mailUrl);
        if (PEAR::isError($status)) {
            $e = new JPSpan_Error();
            $e->setError('ConnectError',$status->getMessage());
            return $e;
        }
        if ( !$conn->getHeaders($mid, $pid) ) {
            $e = new JPSpan_Error();
            $e->setError('MessageError','Unable to fetch message with MID '.$mid);
            return $e;
        }
        $message = array();
        if (!isset($conn->header[$mid]['subject']) || empty($conn->header[$mid]['subject'])) {
            $message['subject'] = 'No subject';
        } else {
            $message['subject'] = trim($conn->header[$mid]['subject']);
        }
        if ( isset($conn->header[$mid]['from_personal'][0]) &&
                !empty($conn->header[$mid]['from_personal'][0]) ) {
            $message['from'] = $conn->header[$mid]['from_personal'][0];
        } else {
            $message['from'] = $conn->header[$mid]['from'][0];
        }
        $message['date'] = date('D, M d, Y h:i:s', $conn->header[$mid]['udate']);
        $body = $conn->getBody($mid, $pid);
        $message['body'] = strip_tags(trim($body['message']));
        $conn->unsetHeaders($mid);
        $conn->close();
        return $message;
    }
}

/**
* Generates a serialized response
*/
class SerializingResponder {
    function execute($payload) {
        $M = & new Mail();
        if ( isset($payload['mid']) && isset($payload['pid']) ) {
            $response = $M->getMessage($payload['mid'],$payload['pid']);
        } else {
            $response = $M->getMessageList();
        }
        echo JPSpan_Serializer::serialize($response);
    }
}

$L = & new JPSpan_Listener();
$L->setResponder(new SerializingResponder());
$L->serve();
?>
