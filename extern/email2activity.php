<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
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

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

// This is a prototype script to convert email messages (raw/EML format)
// to activities. In order to be able to run it, you need to download 
// ezComponents (http://ezcomponents.org/) and store in packages.orig 

/**
 * You can run this file for example with:
 * php display-example.php ../../tests/parser/data/gmail/mail_with_attachment.mail
 */

function formatMail( $mail )
{
    $t = '';
    $t .= "From:      ". formatAddress( $mail->from ). "\n";
    $t .= "To:        ". formatAddresses( $mail->to ). "\n";
    $t .= "Cc:        ". formatAddresses( $mail->cc ). "\n";
    $t .= "Bcc:       ". formatAddresses( $mail->bcc ). "\n";
    $t .= 'Date:      '. date( DATE_RFC822, $mail->timestamp ). "\n";
    $t .= 'Subject:   '. $mail->subject . "\n";
    $t .= "MessageId: ". $mail->messageId . "\n";
    $t .= "\n";
    $t .= formatMailPart( $mail->body );
    return $t;
}

function formatMailPart( $part )
{
    if ( $part instanceof ezcMail )
        return formatMail( $part );

    if ( $part instanceof ezcMailText )
        return formatMailText( $part );

    if ( $part instanceof ezcMailFile )
        return formatMailFile( $part );

    if ( $part instanceof ezcMailRfc822Digest )
        return formatMailRfc822Digest( $part );

    if ( $part instanceof ezcMailMultiPart )
        return formatMailMultipart( $part );

    die( "No clue about the ". get_class( $part ) . "\n" );
}

function formatMailMultipart( $part )
{
    if ( $part instanceof ezcMailMultiPartAlternative )
        return formatMailMultipartAlternative( $part );

    if ( $part instanceof ezcMailMultiPartDigest )
        return formatMailMultipartDigest( $part );

    if ( $part instanceof ezcMailMultiPartRelated )
        return formatMailMultipartRelated( $part );

    if ( $part instanceof ezcMailMultiPartMixed )
        return formatMailMultipartMixed( $part );

    die( "No clue about the ". get_class( $part ) . "\n" );
}

function formatMailMultipartMixed( $part )
{
    $t = '';
    foreach ( $part->getParts() as $key => $alternativePart )
    {
        $t .= formatMailPart( $alternativePart );
    }
    return $t;
}

function formatMailMultipartRelated( $part )
{
    $t = '';
    $t .= "-RELATED MAIN PART-\n";
    $t .= formatMailPart( $part->getMainPart() );
    foreach ( $part->getRelatedParts() as $key => $alternativePart )
    {
        $t .= "-RELATED PART $key-\n";
        $t .= formatMailPart( $alternativePart );
    }
    $t .= "-RELATED END-\n";
    return $t;
}

function formatMailMultipartDigest( $part )
{
    $t = '';
    foreach ( $part->getParts() as $key => $alternativePart )
    {
        $t .= "-DIGEST-$key-\n";
        $t .= formatMailPart( $alternativePart );
    }
    $t .= "-DIGEST END---\n";
    return $t;
}

function formatMailRfc822Digest( $part )
{
    $t = '';
    $t .= "-DIGEST-ITEM-$key-\n";
    $t .= "Item:\n\n";
    $t .= formatMailpart( $part->mail );
    $t .= "-DIGEST ITEM END-\n";
    return $t;
}

function formatMailMultipartAlternative( $part )
{
    $t = '';
    foreach ( $part->getParts() as $key => $alternativePart )
    {
        $t .= "-ALTERNATIVE ITEM $key-\n";
        $t .= formatMailPart( $alternativePart );
    }
    $t .= "-ALTERNATIVE END-\n";
    return $t;
}

function formatMailText( $part )
{
    $t = '';
    $t .= "\n{$part->text}\n";
    return $t;
}

function formatMailFile( $part )
{
    $t = '';
    $t .= "Disposition Type: {$part->dispositionType}\n";
    $t .= "Content Type:     {$part->contentType}\n";
    $t .= "Mime Type:        {$part->mimeType}\n";
    $t .= "Content ID:       {$part->contentId}\n";
    $t .= "Filename:         {$part->fileName}\n";
    $t .= "\n";
    return $t;
}

function formatAddresses( $addresses )
{
    $fa = array();
    foreach ( $addresses as $address )
    {
        $fa[] = formatAddress( $address );
    }
    return implode( ', ', $fa );
}

function formatAddress( $address )
{
    $name = '';
    if ( !empty( $address->name ) )
    {
        $name = "{$address->name} ";
    }
    return $name . "<{$address->email}>";    
}

// initialise CiviCRM
ini_set( 'include_path', '.' . PATH_SEPARATOR . 
                         '..' . PATH_SEPARATOR . 
                         '..' . DIRECTORY_SEPARATOR . 'packages' . PATH_SEPARATOR . 
                         '..' . DIRECTORY_SEPARATOR . 'packages.orig' . PATH_SEPARATOR );

echo ini_get( 'include_path' );

require_once '../civicrm.config.php';
require_once 'CRM/Core/Config.php';
require_once 'api/v2/Activity.php';
require_once 'api/v2/Contact.php';

$config =& CRM_Core_Config::singleton();

require_once 'ezcomponents/Mail/docs/tutorial/tutorial_autoload.php';

// get ready for collecting data about activity to be created
$params = array();
$params['activity_type_id'] = 1; // Frontline Action

// explode email to digestable format
$set = new ezcMailFileSet( array( $argv[1] ) );
$parser = new ezcMailParser();
$mail = $parser->parseMail( $set );


// retrieve sender's email address and
// lookup database contact based on email
// we cannot use civicrm_contact_search, since it uses only primary email
// let's do a direct query
$from_email = $mail[0]->from->email;
$dao =& CRM_Core_DAO::executeQuery( "select contact_id from civicrm_email where email like '{$from_email}'",
                                    CRM_Core_DAO::$_nullArray );
while ( $dao->fetch( ) ) {
    $source_contact_id = $dao->contact_id;
}
if( empty( $source_contact_id ) ) {
  die( "\n\n Source contact with address {$from_email} not found!\n\n" );
}
$params['source_contact_id'] = $params['assignee_contact_id'] = $source_contact_id;


// retrieve first recipient from To: field
$to_email = $mail[0]->to[0]->email;
$dao =& CRM_Core_DAO::executeQuery( "select contact_id from civicrm_email where email like '{$to_email}'",
                                    CRM_Core_DAO::$_nullArray );
while ( $dao->fetch( ) ) {
    $target_contact_id = $dao->contact_id;
}
if( empty( $target_contact_id ) ) {
  die( "\n\n Target contact with address {$to_email} not found!\n\n" );
}
$params['target_contact_id'] = $target_contact_id;

// define other parameters

$params['subject'] = $mail[0]->subject . strtotime( 'now' );

//CRM_Core_Error::debug( 'mail', $mail[0] );
//CRM_Core_Error::debug( 'mail', $mail[0]->getHeader( "Date" ) );

$params['activity_date_time'] = date("YmdHi00",(strtotime($mail[0]->getHeader( "Date" ))));
$params['details'] = formatMailPart( $mail[0]->body );
$params['status_id'] = 1;

//CRM_Core_Error::debug( 's', $params );

// create activity
$msg = civicrm_activity_create( &$params );

// debug
// print_r($msg);


