<?php
ini_set("display_error",1);
header("Access-Control-Allow-origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-type:application/json; charset=utf-8");

require_once '../../../config/connection/connection.php';
require_once '../../../vendor/autoload.php'; 
require_once '../../model/user/user.php';
require_once '../../model/user/merchant.php';
require_once '../../api_response/response.php';
include_once '../../helpers/validator.php';
include_once '../../helpers/mail.php';
include_once '../../language/language.php';

$response= new Response();
if ($_SERVER['REQUEST_METHOD']=== "POST") {

	$data = json_decode(file_get_contents('php://input'));
	$profile_image=Null;
	$validator = new Validator();
	//validating user name & generating respective responses

	if (!empty($data->user_name)) {
		if ($validator->contain_non_alpha_numaric($data->user_name)===0) {
		        $response->set_error_response(null, "only alphabets and numaric values allowed in user name", "400","only use alphabets and number like abc,Abc,123abc,abc123");
		        echo $response->error_respond_api();
		}else{
			$user_name=$data->user_name;
		}
	}else{
		$response->set_error_response(null, "User Name required", "400","user name field empty");
	    echo $response->error_respond_api();
	}

	//validating email format & generating respective responses
	if (!empty($data->email)) {
		if ($validator->validate_email($data->email) === false) {
	        $response->set_error_response(null,"invalid email format", "400","use proper mail like example@example.com");
	        echo $response->error_respond_api();
		}
	}else{
		$response->set_error_response(null, "Email required", "400","email field empty");
	    echo $response->error_respond_api();
	}

	//validating passwrod format & generating respective responses
	if (!empty($data->password) && !empty($data->confirm_password)) {
		if ($data->password === $data->confirm_password) {
			if($validator->validate_password($data->password) === false) {
		        $response->set_response(null,"invalid password format", "400","Password should be at least 8 characters in length and should include set_error_response least one upper case letter, one number, and one special character");
		        echo $response->respond_api();
			}
		}else{
				$response->set_error_response(null, "Password and confirm password not match", "400","passwrod and confirm password not match");
			    echo $response->error_respond_api();
			}
	}else{
		$response->set_error_response(null, "Password and confirm password required", "400","Password and confirm password field empty please fill these");
	    echo $response->error_respond_api();
	}

	//validating first name format & generating respective responses
	if (!empty($data->first_name)) {
		 if ($validator->contain_non_alphabet($data->first_name)) {
		        $response->set_error_response(null, "only alphabets allowed in first name", "400","use proper formate for that Abc,abc,aBc ");
		        echo $response->error_respond_api();
		}
	}else{
		$response->set_error_response(null, "First Name required", "400","first name field empty");
	    echo $response->error_respond_api();
	}

	//validating last name format & generating respective responses
	if (!empty($data->last_name)) {
		 if ($validator->contain_non_alphabet($data->last_name)) {
		        $response->set_error_response(null, "only alphabets allowed in last name", "400","use proper formate for that Abc,abc,aBc");
		        echo $response->error_respond_api();
		}
	}else{
		$response->set_error_response(null, "Last Name required", "400","first name field empty");
	    echo $response->error_respond_api();
	}
	
	//validating contact number & generating respective responses
	if (!empty($data->phone_number)) {
		 if ($validator->contain_non_integer($data->phone_number)) {
		       $response->set_error_response(null,"only integers allowed in phone number","400","use proper formate for that 03********* ");
		        echo $response->error_respond_api();
		}
	}else{
		$response->set_error_response(null, "Phone number required", "400","phone number field empty");
	    echo $response->error_respond_api();
	}
	
	//if data valid, create database object, get connection
	$db=new DataBase();
	$conn = $db->get_connection();
	$user = new User();
	$merchant = new Merchant();
	$mail = new Mail();
	// $email = new \SendGrid\Mail\Mail();
	
    $get_user=$user->find_user("merchants","email",$data->email,$conn); 
	// $user_exist=$user->find_user($data->email,$conn);
	if (empty($get_user)) {
		
	  	// if base64 image upload & generating respective responses
			if (!empty($data->base64_image)) {
			
	            if(preg_match("/^data:image\/(?<extension>(?:png|gif|jpg|jpeg));base64,(?<image>.+)$/", $data->base64_image, $matchings))
	            {
					$save_image=$user->save_image('merchants',$matchings);
				 }else{
	                $response->set_error_response(null, "invalid image format", "400","Image format not match must use .png, .gif, .jpg, .jpeg format");
	                echo $response->error_respond_api();
	            }
			}	
		// create signup for user
		
		$user_name=$data->user_name;
		$email=$data->email;
		$password= password_hash($data->password, PASSWORD_DEFAULT);
		$first_name=$data->first_name;
		$last_name=$data->last_name;
		$phone_number=$data->phone_number;
		$address=$data->address;
		$user_ion_id=uniqid();
		
		//set value of user object
		$user->set_user($user_name,$email,$password,$first_name,$last_name,$phone_number,$address,$user_ion_id,$profile_image);
		try{	
			$create_user=$user->sign_up('merchants',$conn);
			if (!empty($create_user)) {
				$cradit=$merchant->add_credit($user_ion_id,date('Y-m-d G:i:s', strtotime('+30 days')),$conn);
				 $mail->send_registration_mail($data->user_name,$user_ion_id,$data->password,$data->email);

				$response->set_success_response(null, "user create successfully", "200","please check your mail for Key and password for login");
			    echo $response->success_respond_api();
			}
		}catch (Exception $ex) {
			$response->set_error_response(null, $ex->getMessage(), "501","Something Went Worng");
	        echo $response->error_respond_api();
		}
	}else{
		$response->set_error_response(null, "This email already exists", "400","email already exists please use another email");
        echo $response->error_respond_api();
	}
}
