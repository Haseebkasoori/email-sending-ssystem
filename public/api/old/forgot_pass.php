<?php
header("Access-Control-Allow-origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-type:application/json; charset=utf-8");

require_once '../../../config/connection/connection.php';
require_once '../../model/user/user.php';
require_once '../../api_response/response.php';
include_once '../../helpers/validator.php';



$response= new Response();
if ($_SERVER['REQUEST_METHOD']=== "POST") {


}


$response = new Response();
$data = json_decode(file_get_contents('php://input'));


//validating email format & generating respective responses

$validator = new Validator();
if ($validator->validate_email($data->email) === false) 
{
    $response->set_response(null,"invalid email format", "400");
    echo $response->respond_api();
    http_response_code(400);
}else{

		//if data valid, create database object, get connection
		$db=new DataBase();
		$conn = $db->get_connection();
		$user = new User();
		$user_exist=$user->find_user($data->email,$conn);
		//now use set_forgot_function of user object	
}
if(isset($user_exist))
	{
		//if data valid, create database object, get connection
		$to_email = "m.h.kasoori@gmail.com";
		$subject = "Forgot Password OTP  -- ".$user_exist['token']." -- Localhost EMS";
		//creating Email Template for Token/OTP
		$output='<table border="0" width="430" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin:0 auto 0 auto" >
                    <tbody>
                        <tr>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <p style="margin:10px 0 10px 0;color:#565a5c;font-size:18px">Hi User,</p>
                                <p style="margin:10px 0 10px 0;color:#565a5c;font-size:18px">We got a request to reset your EMS password. Here is your OTP</p>
                            </td>
                        </tr>
                        <tr></tr>
                        <tr>
                            <td height="10" style="line-height:10px" colspan="1">&nbsp;</td>
                        </tr>
                        <tr>
                        	<td><strong style="color: #ffff;text-decoration:none;display:block;width: 10em;text-align: center;background: #47a2ea;padding: 1em;font-size: 20px;margin-left: 5em;">'.$user_exist['token'].'
                                </strong></td>
                        </tr>
                        <tr>
                            <td height="10" style="line-height:10px" colspan="1">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <p style="margin:10px 0 10px 0;color:#565a5c;font-size:18px">If you ignore this message, your password will not be changed. If you didn`t request a password reset, <a href="https://support@localhost.com" style="color:#3b5998;text-decoration:none" target="_blank">let us know</a>.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>';
		$body = $output; 
		$headers = "MIME-Version: 1.0" . "\r\n";  
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";  
        $headers .= "From: Localhost EMS";
		 
		if (mail($to_email, $subject, $body, $headers)) 
		{
			$response->set_response(null,"Please Check Your Mail for OTP","200");
			$response->respond_api();
			http_response_code(200);
		}else{
			$response->set_response(null,"Server Problem Try Again Later","500");
			$response->respond_api();
			http_response_code(500);
		}
	}else{
		$response->set_response(null,"Mail not found in our Database","404");
		$response->respond_api();
		http_response_code(404);
	}