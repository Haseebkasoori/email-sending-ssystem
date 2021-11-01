<?php
ini_set("display_error",1);
header("Access-Control-Allow-origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-type:application/json; charset=utf-8");

require_once '../../../config/connection/connection.php';
require_once '../../../vendor/autoload.php'; 
require_once '../../model/user/user.php';
require_once '../../api_response/response.php';
include_once '../../helpers/validator.php';
include_once '../../helpers/mail.php';
include_once '../../language/language.php';


$response= new Response();
if ($_SERVER['REQUEST_METHOD']=== "POST") {
	$data = json_decode(file_get_contents('php://input'));

	
	//create objects.
	$db=new DataBase();
	$conn = $db->get_connection();
	$response = new Response();
	$user = new User();
	$mail = new Mail();

	//validating email format & generating respective responses

	$validator = new Validator();

	if (isset($data->user_id)) {
        $get_user=$user->find_user("admin",'user_ion_id',$data->user_id,$conn);
	}else if (isset($data->email)) {
	        $get_user=$user->find_user("admin",'email',$data->email,$conn);

	        if ($validator->validate_email($data->email) === True) 
			{
				$response->set_error_response(null,"invalid email format", "404","Please type proper email address");
		    	echo $response->error_respond_api();	
			}
			
		}
		if (!empty($get_user)) {
			//now use set_otp of user object
			$otp=$user->set_otp('admin',$get_user['user_ion_id'],$conn);
			if(isset($otp))
			{
				
				try{
									
					// send mail
					$mail->send_forgot_mail($get_user['email'],$get_user['user_ion_id'],$otp);

					//send response
					$response->set_success_response(null,"OTP send successfully on mail", "200","Please check you OTP in your mail inbox or spam");
	                echo $response->success_respond_api();
				}catch(Exception $ex){

					$response->set_error_response(null,$ex->getMessage(), "500","Somthing went worng");
	                echo $response->error_respond_api();
				}
			}else{

				$response->set_error_response(null,"Server Problem Try Again Later", "500","Somthing went worng");
	            echo $response->error_respond_api();
			}
	}else{

		$response->set_error_response(null,"No User found", "404","Given data not register please register your self");
        echo $response->error_respond_api();
	}
}