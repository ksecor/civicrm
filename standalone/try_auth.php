<?php

require_once "auth_common.php";

function getOpenIDURL() {
    // Render a default page if we got a submission without an openid
    // value.
    if (empty($_REQUEST['openid_identifier'])) {
        $error = "Expected an OpenID URL.";
        include 'index.php';
        exit(0);
    }
    
    return $_REQUEST['openid_identifier'];
}

function run() {
    $openid   = getOpenIDURL();
    $consumer = getConsumer();

    // Begin the OpenID authentication process.
    $auth_request = $consumer->begin($openid);

    // No auth request means we can't begin OpenID.
    if (!$auth_request) {
        // check for new install, if no, go to index, else goto new-install page
        require_once 'CRM/Core/BAO/UFMatch.php';
        $contactIds = CRM_Core_BAO_UFMatch::getContactIDs();

        if ( count($contactIds) > 0 ) {
            displayError("Authentication error; not a valid OpenID.");
        } else {
            header("Location: new_install.php"); 
            exit(1);
        }
    }

    $sreg_request = Auth_OpenID_SRegRequest::build(
                                                   // Required
                                                   array('nickname'),
                                                   // Optional
                                                   array('fullname', 'email'));

    if ($sreg_request) {
        $auth_request->addExtension($sreg_request);
    }

    $policy_uris = array();
    if ( isset($_REQUEST['policies']) ) {
        $policy_uris = $_REQUEST['policies'];
    }

    $pape_request = new Auth_OpenID_PAPE_Request($policy_uris);
    if ($pape_request) {
        $auth_request->addExtension($pape_request);
    }

    // Redirect the user to the OpenID server for authentication.
    // Store the token for this authentication so we can verify the
    // response.

    // For OpenID 1, send a redirect.  For OpenID 2, use a Javascript
    // form to send a POST request to the server.
    if ($auth_request->shouldSendRedirect()) {
        $redirect_url = $auth_request->redirectURL(getTrustRoot(),
                                                   getReturnTo());
        
        // If the redirect URL can't be built, display an error
        // message.
        if (Auth_OpenID::isFailure($redirect_url)) {
            displayError("Could not redirect to server: " . $redirect_url->message);
        } else {
            // Send redirect.
            header("Location: ". $redirect_url);
            exit(2);
        }
    } else {
        // Generate form markup and render it.
        $form_id = 'openid_message';
        $form_html = $auth_request->htmlMarkup(getTrustRoot(), getReturnTo(),
                                               false, array('id' => $form_id));

        // Display an error if the form markup couldn't be generated;
        // otherwise, render the HTML.
        if (Auth_OpenID::isFailure($form_html)) {
            displayError("Could not redirect to server: " . $form_html->message);
        } else {
            print $form_html;
        }
    }
}

run();
