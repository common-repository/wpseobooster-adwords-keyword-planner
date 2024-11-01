<?php
require __DIR__ . '/../vendor/autoload.php';

use Google\Auth\OAuth2;


defined('ABSPATH') || exit;

define('GAKP_CLIENTID', get_option("gakp_client_id"));
define('GAKP_CLIENTSECRET', get_option('gakp_client_secret'));
define('GAKP_REDIRECT_URI', get_option('gakp_redirect_url'));
define('GAKP_AUTHORIZATION_URI', 'https://accounts.google.com/o/oauth2/v2/auth');
define('GAKP_TOKEN_CREDENTIAL_URI', 'https://www.googleapis.com/oauth2/v4/token');
define('GAKP_ADWORDS_API_SCOPE', 'https://www.googleapis.com/auth/adwords');
define('GAKP_CUSTOMER_CLIENT_ID', get_option('gakp_customer_client_id'));
define('GAKP_DEV_TOKEN', 'O3DN_-MpR9tcxXxFouXr2g');

class Gakp_Get_Authenticate_With_Google
{
    public static function create_oauth2_instance()
    {
        session_start();
        $oauth2 = new OAuth2([
            'authorizationUri' => GAKP_AUTHORIZATION_URI,
            'tokenCredentialUri' => GAKP_TOKEN_CREDENTIAL_URI,
            'redirectUri' => GAKP_REDIRECT_URI,
            'clientId' => GAKP_CLIENTID,
            'clientSecret' => GAKP_CLIENTSECRET,
            'scope' => GAKP_ADWORDS_API_SCOPE,
            'state' => Gakp_Keyword_Planner::gakp_current_url()
        ]);
        return $oauth2;
    }

    public static function generate_login_uri()
    {
        $oauth2 = self::create_oauth2_instance();
        $uri = $oauth2->buildFullAuthorizationUri();
        return $uri;
    }
    public static function get_refresh_token()
    {
        if (isset($_GET['code'])) {
            delete_option('gakp_google_refresh_code');
            $oauth2 = self::create_oauth2_instance();
            $oauth2->setCode(sanitize_text_field($_GET['code']));
            $authToken = $oauth2->fetchAuthToken();
            // Store the refresh token for your user in your local storage if you
            // requested offline access.
            $refreshToken = $authToken['refresh_token'];
            update_option('gakp_google_refresh_code', $refreshToken);
            wp_redirect(sanitize_text_field($_GET['state']));
            exit;
        }
    }
}
