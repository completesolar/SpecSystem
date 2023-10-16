<?php
// Include the required dependencies
require 'vendor/autoload.php';

// Create a new Google Client
$client = new Google_Client();
$client->setClientId(getenv('GOOGLE_CLIENT_ID'));
$client->setClientSecret(getenv('GOOGLE_SECRET_ID'));
$client->setRedirectUri(getenv('REDIRECT_URL'));
$client->addScope(Google_Service_Oauth2::USERINFO_EMAIL);

/**
 * Google_Client()  -> Set account config params
 * userinfo_v2_me() -> set session Mail
 * verifyIdToken()  -> Validate user JWT token or redirect
 * Set Session params
 * Redirect to: Origin || App
 */
if (isset($_GET['code'])) {

    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $id_token = $token['id_token'];

    try {
        $client->setAccessToken($token['access_token']);
        $oAuth = new Google_Service_Oauth2($client);
        $userData = $oAuth->userinfo_v2_me->get();
        $_SESSION['email'] = $userData['email'];

    } catch (Exception $e) {
        print_r(["EXCEPTION--> ", $e->getTraceAsString()]);
    }

    //Validate jwt against google and set session params
    $payload = $client->verifyIdToken($id_token);

    if ($payload) {
        $_SESSION['state'] = $payload['sub'];;
        $_SESSION['jwt_token'] = $id_token;
        $_SESSION['login_type'] = "google";
        require_once 'app/submit_form.php';

    } else {
        // Invalid ID token
        error_log("INVALID TOKEN X-X-X-X $payload");
        $loginUrl = $client->createAuthUrl();
        header('Location: ' . $loginUrl);
        exit();
    }

} else {
    $loginUrl = $client->createAuthUrl();
    header('Location: ' . $loginUrl);
    exit();
}
