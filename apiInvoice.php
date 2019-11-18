<?php

require_once(__DIR__ . '/vendor/autoload.php');
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Invoice;

session_start();

function createInvoice()
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

    /*
     * Retrieve the accessToken value from session variable
     */
    $accessToken = $_SESSION['sessionAccessToken'];

    /*
     * Update the OAuth2Token of the dataService object
     */
    $dataService->updateOAuth2Token($accessToken);
    $dataService->setLogLocation("/Users/hlu2/Desktop/newFolderForLog");
    $dataService->throwExceptionOnError(true);
    // Add a new Invoice
    $theResourceObj = Invoice::create([
        "Line" => [
        [
             "Amount" => 22.5,
             "DetailType" => "SalesItemLineDetail",
             "SalesItemLineDetail" => [
               "ItemRef" => [
                 "value" => 5
                //  "name" => "Services"
               ]
             ]
        ]
        ],
        "CustomerRef"=> [
              "value"=> 3333
        ],
        "BillEmail" => [
              "Address" => "customer@example.com"
        ],
        //
        // "ShipFromAddr" =>[
        //     "Line1"           => "Dave's Ice Cream Shop",
        //     "Line2"           => "123 South Broad Street",
        //     "City"            => "Philadelphia",
        //     "CountrySubDivisionCode" => "PA",
        //     "PostalCode"      => "19109"
        // ]
    ]);
    $resultingObj = $dataService->Add($theResourceObj);
    $error = $dataService->getLastError();
    if ($error) {
        echo "The Status code is: " . $error->getHttpStatusCode() . "\n";
        echo "The Helper message is: " . $error->getOAuthHelperError() . "\n";
        echo "The Response message is: " . $error->getResponseBody() . "\n";
    }
    else {
        echo "Created Id={$resultingObj->Id}. Reconstructed response body:\n\n";
        $xmlBody = XmlObjectSerializer::getPostXmlFromArbitraryEntity($resultingObj, $urlResource);
        echo $xmlBody . "\n";
    }


}

$result = createInvoice();

?>
