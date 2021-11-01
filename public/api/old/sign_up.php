<?php
ini_set("display_error",1);
header("Access-Control-Allow-origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-type:application/json; charset=utf-8");

require_once '../../../config/connection/connection.php';
require_once '../../model/user/user.php';
require_once '../../api_response/response.php';
include_once '../../helpers/validator.php';
include_once '../../language/language.php';


$response= new Response();
if ($_SERVER['REQUEST_METHOD']=== "POST") {

	$data = json_decode(file_get_contents('php://input'));
	$profile_image="";
	//validating user name & generating respective responses
	$validator = new Validator();

	if (!empty($data->user_name)) {
		if ($validator->contain_non_alpha_numaric($data->user_name)===0) {
		        $response->set_response(null, "only alphabets and numaric values allowed in user name", "400");
		        http_response_code(400);
		        echo $response->respond_api();
		}else{
			$user_name=$data->user_name;
		}
	}else{
		$response->set_response(null, "User Name required", "400");
	    http_response_code(400);
	    echo $response->respond_api();
	}

	//validating email format & generating respective responses
	if (!empty($data->email)) {
		if ($validator->validate_email($data->email) === false) {
	        $response->set_response(null,"invalid email format", "400");
	        http_response_code(400);
	        echo $response->respond_api();
		}
	}else{
		$response->set_response(null, "Email required", "400");
	    http_response_code(400);
	    echo $response->respond_api();
	}

	//validating passwrod format & generating respective responses
	if (!empty($data->password) && !empty($data->confirm_password)) {
		if ($data->password === $data->confirm_password) {
			if($validator->validate_password($data->password) === false) {
		        $response->set_response(null,"Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character", "400");
		        http_response_code(400);
		        echo $response->respond_api();
			}
		}else{
				$response->set_response(null, "Password and confirm password not match", "400");
			    http_response_code(400);
			    echo $response->respond_api();
			}
	}else{
		$response->set_response(null, "Password and confirm password required", "400");
	    http_response_code(400);
	    echo $response->respond_api();
	}

	//validating first name format & generating respective responses
	if (!empty($data->first_name)) {
		 if ($validator->contain_non_alphabet($data->first_name)) {
		        $response->set_response(null, "only alphabets allowed in first name", "400");
		        http_response_code(400);
		        echo $response->respond_api();
		}
	}else{
		$response->set_response(null, "First Name required", "400");
	    http_response_code(400);
	    echo $response->respond_api();
	}

	//validating last name format & generating respective responses
	if (!empty($data->last_name)) {
		 if ($validator->contain_non_alphabet($data->last_name)) {
		        $response->set_response(null, "only alphabets allowed in last name", "400");
		        http_response_code(400);
		        echo $response->respond_api();
		}
	}else{
		$response->set_response(null, "Last Name required", "400");
	    http_response_code(400);
	    echo $response->respond_api();
	}
	//validating contact number & generating respective responses
	if (!empty($data->phone_number)) {
		 if ($validator->contain_non_integer($data->phone_number)) {
		        $response->set_response(null, "only integers allowed in phone number", "400");
		        http_response_code(400);
		        echo $response->respond_api();
		}
	}else{
		$response->set_response(null, "Phone number required", "400");
	    http_response_code(400);
	    echo $response->respond_api();
	}
	// // if base64 image upload & generating respective responses
	// if (!empty($data->base64_image)) {
	// 	// $base64 = "data:image/png;base64,gAAAQ8AAAC6CAMAAACHgTh+AA=";
		
	// 	if(preg_match("/^data:image\/(?<extension>(?:png|gif|jpg|jpeg));base64,(?<image>.+)$/", $data->base64_image, $matchings))
	// 	{
	// 	   $imageData = base64_decode($matchings['image']);
	// 	   $extension = $matchings['extension'];
	// 	   $filepath=UPLOAD_DIR . uniqid().".%s";
	// 	   $filename = sprintf($filepath, $extension);
	// 	   if($data=file_put_contents($filename, $imageData))
	// 	   {
	// 	   	$profile_image=$filename;
	// 	   }
	// 	}else{
	// 		$response->set_response(null, "Image format not match must use .png, .gif, .jpg, .jpeg format", "400");
	// 	    http_response_code(400);
	// 	    echo $response->respond_api();
	// 	}
	// }

	//if data valid, create database object, get connection
	$db=new DataBase();
	$conn = $db->get_connection();
	$user = new User();
	
	print_r($data->email);
	$user_exist=$user->find_user($data->email,$conn);
	
	print_r($profile_image);
	print_r($user_exist);
	exit;

	$password= password_hash($data->password, PASSWORD_DEFAULT);

}


		// create object of database class
		$db=new DataBase();

		// create &conn and call get_connection() to get connection identifier

		$d=$d->get_connection();
//      // create user object //$emp = new User();
//      $u=new User();
//      // call sign_up() method and pass required parameters //$user->sign_up(all required params)
//      $u->set_user(1,'Haris','hkhurshid95@gmail.com','hkkgkh','03328681588','1');
//      $result=$u->sign_up($email,$password,$confirm_password,$d);
     
//      if($result==true)
//      {
//           $response->set_response(NULL,"sign up success",200);
//           $response->respond_api();
//      }
     
//      if($result==false)
//      {
//           $response->set_response(NULL,"Unsuccessful signup",406);
//           $response->respond_api();
//      }
// }
