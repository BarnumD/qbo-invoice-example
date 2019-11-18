<?php

require_once(__DIR__ . '/vendor/autoload.php');
use QuickBooksOnline\API\DataService\DataService;

session_start();

function processCode()
{

    // Create SDK instance
    $config = include('config.php');
    $dataService = DataService::Configure(array(
        'auth_mode' => 'oauth2',
        'ClientID' => $_ENV['qb_client_id'],
        'ClientSecret' =>  $_ENV['qb_client_secret'],
        'RedirectURI' => $_ENV['qb_oauth_redirect_uri'],
        'scope' => $_ENV['qb_oauth_scope'],
        'baseUrl' => $_ENV['qb_base_url']
    ));

    $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
    $parseUrl = parseAuthRedirectUrl($_SERVER['QUERY_STRING']);

    /*
     * Update the OAuth2Token
     */
    $accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($parseUrl['code'], $parseUrl['realmId']);
    $dataService->updateOAuth2Token($accessToken);

    /*
     * Setting the accessToken for session variable
     */
    $_SESSION['sessionAccessToken'] = $accessToken;
}

function parseAuthRedirectUrl($url)
{
    parse_str($url,$qsArray);
    return array(
        'code' => $qsArray['code'],
        'realmId' => $qsArray['realmId']
    );
}

$result = processCode();

?>