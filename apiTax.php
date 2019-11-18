<?php

require_once(__DIR__ . '/vendor/autoload.php');
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Invoice;

session_start();

function getTaxAgenies()
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

    $taxagencies = $dataService->FindAll('taxagency');

    $error = $dataService->getLastError();
    if ($error) {
        echo "The Status code is: " . $error->getHttpStatusCode() . "\n";
        echo "The Helper message is: " . $error->getOAuthHelperError() . "\n";
        echo "The Response message is: " . $error->getResponseBody() . "\n";
    }
    else {
        echo "Tax Agencies:<br>";
        foreach ($taxagencies as $taxAgency){
            $output =  "ID: " . $taxAgency->Id . " DisplayName: " . $taxAgency->DisplayName . "<br>";
            echo $output;
            // var_dump($taxAgency);
        }
    }

    $taxCodes = $dataService->FindAll('taxcode');
    $error = $dataService->getLastError();
    if ($error) {
        echo "The Status code is: " . $error->getHttpStatusCode() . "\n";
        echo "The Helper message is: " . $error->getOAuthHelperError() . "\n";
        echo "The Response message is: " . $error->getResponseBody() . "\n";
    }
    else {
        echo "<br>Tax Codes:<br>";
        foreach ($taxCodes as $taxCode){
            $output =  "Name: " . $taxCode->Name . " Description: '" . $taxCode->Description . "' Taxgroup: '" . $taxCode->TaxGroup . "' Active: '" . $taxCode->Active . "'<br>";
            echo $output;
            if (isset($taxCode->SalesTaxRateList)){
                // var_dump($taxCode->SalesTaxRateList->TaxRateDetail);
                echo "  TaxRateDetail:<br>";

                foreach ($taxCode->SalesTaxRateList->TaxRateDetail as $taxRateDetail){
                    // var_dump($taxRateDetail);

                    $output = "  TaxRateRef: '" . $taxRateDetail->TaxRateRef . "' TaxTypeApplicable: '" . $taxRateDetail->TaxTypeApplicable . "' TaxOnAmount: '" . $taxRateDetail->TaxOnAmount . "' TaxOrder: '" . $taxRateDetail->TaxOrder . "' TaxOnTaxOrder: '". $taxRateDetail->TaxOnTaxOrder . "'<br>";
                    echo $output;
                }
            }
        }
    }

    $taxRates = $dataService->FindAll('taxrate');
    $error = $dataService->getLastError();
    if ($error) {
        echo "The Status code is: " . $error->getHttpStatusCode() . "\n";
        echo "The Helper message is: " . $error->getOAuthHelperError() . "\n";
        echo "The Response message is: " . $error->getResponseBody() . "\n";
    }
    else {
        echo "<br>Tax Rates:<br>";
        foreach ($taxRates as $taxRate){
            // var_dump($taxRate);

            $output =  "Id: '". $taxRate->Id . "' Name: '" . $taxRate->Name . "' Description: '" . $taxRate->Description . "' Active: '" . $taxRate->Active . "' RateValue: '" . $taxRate->RateValue . "' AgencyRef: '" . $taxRate->AgencyRef . "' TaxReturnLineRef: '". $taxRate->TaxReturnLineRef ."'<br>";
            echo $output;
        }
    }
}

$result = getTaxAgenies();

?>
