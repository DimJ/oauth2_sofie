<?php

	function createTokenENCRYPT_0( $symetricKey )
	{
		$jwt = new \OAuth2\Encryption\Jwt();
	    $header = array( 1 => 3 );
	    $payload = produceCWT_claims( $jwt, $symetricKey );
	    $payload = cborEncode( $payload );
	    list( $A, $IV, $C ) = encryptAESGCM( $symetricKey, $payload );
	    return array( $A, $IV, array($header, $C) ); // THIS IS NOT CORRECT!!!
	}

	function createTokenMAC_0( $secretKey )
	{
		$jwt = new \OAuth2\Encryption\Jwt();
	    $header = array( 1 => 5, 3 => "MAC0" );
	    $payload = produceCWT_claims( $jwt );
	    $payload = cborEncode( $payload );
	    $tag = hs256_tag( $payload, $secretKey );
		return array( $header, "payload" => $payload, "tag" => $tag );
	}

	function createTokenSignature_1()
	{
		$jwt = new \OAuth2\Encryption\Jwt();
	    $header = array( 3 => "Signature1" );
	    $payload = produceCWT_claims( $jwt );
	    $payload = cborEncode( $payload );
	    $signature = createSignature( bin2hex($payload) ); // hs256_tag( $payload, $secretKey );
	    $signatureHeader = array( 1 => -7 );
	    $signatures = array( array( $signatureHeader, "signature" => $signature ) );
	    return array( $header, "payload" => $payload, "signatures" => $signatures );
	}

	function encryptAESGCM( $symetricKey, $payload )
	{
		$A = hex2bin('feedfacedeadbeef'); // Additional Authenticated Data
		$IV = hex2bin('cafebabefacedbad'); // Initialization Vector
		$C = \OAuth2\CborWebToken\AESGCM::encryptAndAppendTag($symetricKey, $IV, $payload, $A); 
		return array( $A, $IV, $C );
	}


	function cborEncode( $data )
	{
		return \OAuth2\CborWebToken\CBOREncoder::encode($data);
	}


	function produceCWT_claims( $jwt )
	{
		$payload = array(
						7=> random_bytes(16), 	
						1=>"AS_1", 
    					2=>"Auth", 
    					3=>"Clnt", 
    					4=>time(), 
    					6=>time(),
    					30=>"bear",
    					31=>"ACCS"
    				);
		return $payload;
	}

	function hs256_tag( $input, $key )
	{
		$jwt = new \OAuth2\Encryption\Jwt();
		$signedTag = $jwt->sign( $input, $key );
		return $signedTag;
	}


	function publicKeyEncrypt( $plaintext, $publicKey )
    {
        openssl_public_encrypt($plaintext, $encrypted, $publicKey);
        return $encrypted;
    }

    function changeBytesArray( $byteArray )
    {
    	$transformedArray = array();
    	foreach( $byteArray as $byteValue )
    	{
    		$newValue = dechex($byteValue);
    		if( strlen($newValue) == 1 )
    			$newValue = ('0'.$newValue);
    		$newValue = ('0x'.$newValue);
    		$transformedArray[] = $newValue;
    	}
    	$finalString = implode( ",", $transformedArray );

    	return $finalString;
    }

    function createSignature( $payloadInHex )
    {
    	$ec = new Elliptic\EC('secp256k1'); // Basic curve for Bitcoin & Ethereum.
		$key = $ec->genKeyPair();
		$signature = $key->sign($payloadInHex);
		$derSign = $signature->toDER('hex');
		return hex2bin($derSign);
    }

?>