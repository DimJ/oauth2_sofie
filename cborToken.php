<?php
	/*
	* The CBOR Web Token (CWT) is an extension for the SOFIE project. 
	* We utilized RFC 8392 (https://tools.ietf.org/html/rfc8392).
	* This script implements the two first models from WF-IoT 
	* (https://mm.aueb.gr/publications/2019-WF-IoT.pdf).
	*
    * The output is a json with the following fields:
    * Scenario 1
    *	"e_s_token"
	*	"e_thing_pop"
	*	"pop"
	*	"h"
	*	"price"
	*	"rest_of_info_hash"
	* Scenario 2
	*	"e_s_token"
	*	"e_thing_pop"
	*	"e_client_pop"
	*	"h"
	*	"price"
	*/

	ini_set('display_errors',1);error_reporting(E_ALL);

    require_once('oauth2-server-php/src/Autoloader.php');
    Autoloader::register();

    require_once('./createCWT_token.php');

    $symetricKey = trim(file_get_contents('/home/dimitris/Desktop/OAuth2BlockChainKeys/AS1Thingkey'));
    $publicKey = trim(file_get_contents('/home/dimitris/Desktop/OAuth2BlockChainKeys/pubkey.pem'));

    $s_key = bin2hex(random_bytes(8)); // secret key
    $pop = bin2hex(random_bytes(8)); 
	$price = rand(1000000000, 5000000000);


    if( strcmp( $_GET["typeOfScenario"], "scenario1") == 0 )
    {
	    $CWT_token = createTokenMAC_0( $s_key ); 
	    $encodedCWT = \OAuth2\CborWebToken\CBOREncoder::encode($CWT_token);
		list($A, $IV, $encryptedCWT) = encryptAESGCM( $s_key, $encodedCWT );
		$encodedCWT_messageToServer = base64_encode($encryptedCWT);
	    list($A, $IV, $e_thing_pop) = encryptAESGCM( $symetricKey, $pop );
		$encodedEthingPop_messageToServer = base64_encode($e_thing_pop);
	    $h = \OAuth2\Keccak256::hash($s_key, 256);
	    $restΟfΙnfo = \OAuth2\Keccak256::hash( ($encodedEthingPop_messageToServer.$pop.$encodedCWT_messageToServer), 256);
	    $data = array
	    (
	    	"e_s_token" => $encodedCWT_messageToServer,
	    	"e_thing_pop" => $encodedEthingPop_messageToServer,
	    	"pop" => $pop,
	    	"h" => $h,
	    	"price" => $price,
	    	"rest_of_info_hash" => $restΟfΙnfo,
	    	"s_key" => $s_key
	    );
	    print( json_encode($data) );
    }
    else if( strcmp( $_GET["typeOfScenario"], "scenario2") == 0 )
    {
    	$CWT_token = createTokenMAC_0( $s_key ); 
    	$encodedCWT = \OAuth2\CborWebToken\CBOREncoder::encode($CWT_token);
		list($A, $IV, $encryptedCWT) = encryptAESGCM( $s_key, $encodedCWT );
		$encodedCWT_messageToServer = base64_encode($encryptedCWT);
	    list($A, $IV, $e_thing_pop) = encryptAESGCM( $symetricKey, $pop );
		$encodedEthingPop_messageToServer = base64_encode($e_thing_pop);
	    $e_client_pop = publicKeyEncrypt( $pop, $publicKey );
	    $encodedEclientPop_messageToServer = base64_encode($e_client_pop);
	    $h = \OAuth2\Keccak256::hash($s_key, 256);
	    $data = array
	    (
	    	"e_s_token" => $encodedCWT_messageToServer,
	    	"e_thing_pop" => $encodedEthingPop_messageToServer,
	    	"e_client_pop" => $encodedEclientPop_messageToServer,
	    	"h" => $h,
	    	"price" => $price,
	    	"s_key" => $s_key
	    );
	    print( json_encode($data) );
    }

?>