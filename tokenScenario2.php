<?php
    /*
    * For the second scenario of  WF-IoT (https://mm.aueb.gr/publications/2019-WF-IoT.pdf),
    * we extended the library. 
    *
    * The output is a json with the following fields:
    *    "expires_in"
    *    "token_type"
    *    "scope"
    *    "e_s_token"
    *    "e_thing_pop"
    *    "e_client_pop",
    *    "price"
    *    "h"
    * 
    */
    ini_set('display_errors',1);error_reporting(E_ALL);

    require_once('oauth2-server-php/src/Autoloader.php');
    Autoloader::register();

    /*
    *  For the secons scenario, we insert a key (AS1ThingKey) for symmetric encryption, 
    *  between the AS and the Thing. We also insert a public key for asymmetric enpryption of
    *  the Client with the SA  
    */
    $AS1ThingKey = '/home/dimitrios-d/OAuth2BlockChainKeys/AS1Thingkey'; 
    $publicKey = '/home/dimitrios-d/OAuth2BlockChainKeys/pubkey.pem';
    $symetricKey = trim(file_get_contents($AS1ThingKey));

    $storage = new OAuth2\Storage\Memory(array(
        'keys' => array(
            'private_key' => $symetricKey,  // The appropriate key for HS256.
            'encryption_algorithm'  => 'HS256' // "RS256" is the default
        ),
        // add a Client ID for testing
        'client_credentials' => array(
            'theClient' => array('client_secret' => 'thePassword')
        ),
    ));

    $server = new OAuth2\Server($storage, array(
        'use_jwt_access_tokens_sophie_experiments' => true,
        'issuer' => 'AS_1',
        'subject' => 'SFA1',   
        'scope' => 'Blockchain with IoT'
    ));

    $server->setKeys($AS1ThingKey, $publicKey);

    $server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage)); 

    $server->handleTokenRequestForSophie( "Scenario2", OAuth2\Request::createFromGlobals())->send();

?>