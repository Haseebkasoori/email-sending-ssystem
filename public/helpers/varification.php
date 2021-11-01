<?php 

require_once '../../../vendor/autoload.php';

use\Firebase\JWT\JWT;	
	/**
	 * 
	 */
	class Varification
	{
	    public $Auth_key;

	    public function set_key($user_data)
	    {
	        
            $secret_key="P0551BL3";
            
            $iss = "localhost";
            $iat = time(); 
            $nbf = $iat+10; 
            $exp = $iat+1800; 
            $aud = "Merchant"; 
            $user_data_array = $user_data;

            $payload_info= array(
                "iss" =>$iss,
                "iat" =>$iat, 
                "nbf" =>$nbf,
                "exp" =>$exp, 
                "aud" =>$aud, 
                "data" =>$user_data_array
                );
            try {
                $Auth_key = JWT::encode($payload_info,$secret_key);
            	return array('Auth_key'=>$Auth_key);
            } catch (Exception $e) {
            	return array('error'=>$e->getMessage());
            }
	    }

	    public function varify_key($auth_key)
	    {
	        
            $secret_key="P0551BL3";
            
            $iss = "localhost";
            $iat = time(); 
            $nbf = $iat+10; 
            $exp = $iat+1800; 
            $aud = "Merchant"; 
            $user_data_array = $user_data;

            $payload_info= array(
                "iss" =>$iss,
                "iat" =>$iat, 
                "nbf" =>$nbf,
                "exp" =>$exp, 
                "aud" =>$aud, 
                "data" =>$user_data_array
                );
            try {
                $Auth_key = JWT::encode($payload_info,$secret_key);
            	return array('Auth_key'=>$Auth_key);
            } catch (Exception $e) {
            	return array('error'=>$e->getMessage(),'Auth_key'=>Null);
            }
	    }

    public function get_user_data($jwt_key){

        try {
                $secret_key="P0551BL3";
                $decoded_data = JWT::decode($jwt_key,$secret_key,array_keys(JWT::$supported_algs));
                return array('decoded_data'=>$decoded_data);
            } catch (Exception $e) {
                return array('error'=>$e->getMessage(),'decoded_data'=>Null);
            }


    }


	}