<?php
ini_set("display_error",1);
header("Access-Control-Allow-origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-type:application/json; charset=utf-8");

require_once '../../../config/connection/connection.php';
require_once '../../../vendor/autoload.php'; 
require_once '../../model/user/secondaryUser.php';
require_once '../../api_response/response.php';
include_once '../../helpers/validator.php';
include_once '../../helpers/varification.php';
include_once '../../language/language.php';

$response= new Response();
if ($_SERVER['REQUEST_METHOD']=== "POST") {

	$data = json_decode(file_get_contents('php://input'));

	    $varification = new Varification();

	    // Get Auth key for varification
	    $all_headers=getallheaders();
	    $jwt_key=$all_headers['Auth_key'];
	    if (!empty($jwt_key)) {
	        try{

	        	//if data valid, create database object, get connection
				$db=new DataBase();
				$conn = $db->get_connection();
				$user = new SecondaryUser();
				// $email = new \SendGrid\Mail\Mail();
				
				$user_exist=$user->check_user($data->sec_user_id,$conn);
				if (!empty($user_exist['user_name'])) {
					// create signup for user
					$merchat_id=$user_exist['merchant_id'];
					$user_ion_id=$data->sec_user_id;
					$user_name=$user_exist['user_name'];
					$email=$user_exist['email'];
					$password= $user_exist['password'];
					$first_name=$user_exist['first_name'];
					$last_name=$user_exist['last_name'];
					$phone_number=$user_exist['phone_number'];
					$address=$user_exist['address'];
					$profile_image=$user_exist['profile_image'];
					$email_sending=$user_exist['email_sending'];
					$cradit_recharge=$user_exist['cradit_recharge'];

					$validator = new Validator();
					//validating user name & generating respective responses
					$varify=$varification->get_user_data($jwt_key);
	                if(!empty($varify['decoded_data'])){
	                    
	                    if (!empty($data->user_name)) {
							if ($validator->contain_non_alpha_numaric($data->user_name)===0) {
							        $response->set_error_response(null, "only alphabets and numaric values allowed in user name", "400","only use alphabets and number like abc,Abc,123abc,abc123");
							        http_response_code(400);
							        echo $response->error_respond_api();
							}else{
								$user_name=$data->user_name;
							}
						}

						//validating email format & generating respective responses
						if (!empty($data->email)) {
							if ($validator->validate_email($data->email) === false) {
						        $response->set_error_response(null,"invalid email format", "400","use proper mail like example@example.com");
						        http_response_code(400);
						        echo $response->error_respond_api();
							}else{
								$email=$data->email;
							}
						}

	                    //validating passwrod format & generating respective responses
	                    if (!empty($data->password) or !empty($data->confirm_password)) {
	                        if ($data->password === $data->confirm_password) {
	                            if($validator->validate_password($data->password) === false) {
	                                $response->set_response(null,"invalid password format", "400","Password should be at least 8 characters in length and should include set_error_response least one upper case letter, one number, and one special character");
	                                http_response_codeerror_(400);
	                                echo $response->respond_api();
	                            }else
	                                $password=password_hash($data->password, PASSWORD_DEFAULT);
	                        }else{
	                                $response->set_error_response(null, "Password and confirm password not match", "400","passwrod and confirm password not match");
	                                http_response_code(400);
	                                echo $response->error_respond_api();
	                            }
	                    }else{
	                        $response->set_error_response(null, "Password and confirm password required", "400","Password and confirm password field empty please fill these");
	                        http_response_code(400);
	                        echo $response->error_respond_api();
	                    }

						//validating first name format & generating respective responses
						if (!empty($data->first_name)) {
							 if ($validator->contain_non_alphabet($data->first_name)) {
							        $response->set_error_response(null, "only alphabets allowed in first name", "400","use proper formate for that Abc,abc,aBc ");
							        http_response_code(400);
							        echo $response->error_respond_api();
							}
						}

						//validating last name format & generating respective responses
						if (!empty($data->last_name)) {
							 if ($validator->contain_non_alphabet($data->last_name)) {
							        $response->set_error_response(null, "only alphabets allowed in last name", "400","use proper formate for that Abc,abc,aBc");
							        http_response_code(400);
							        echo $response->error_respond_api();
							}
						}
						//validating contact number & generating respective responses
						if (!empty($data->phone_number)) {
							 if ($validator->contain_non_integer($data->phone_number)) {
							       $response->set_error_response(null,"only integers allowed in phone number","400","use proper formate for that 03********* ");
							        http_response_code(400);
							        echo $response->error_respond_api();
							}
						}
						//validating email permission & generating respective responses
						if (!empty($data->email_sending)) {
							 if (preg_match("/[^0-1]+/",$data->email_sending)) {
							       $response->set_error_response(null,"invalid data","400","use 0 or 1 for permissoin");
							        http_response_code(400);
							        echo $response->error_respond_api();
							}
						}//validating contact number & generating respective responses
						if (!empty($data->phone_number)) {
							 if ($validator->contain_non_integer($data->phone_number)) {
							       $response->set_error_response(null,"only integers allowed in phone number","400","use proper formate for that 03********* ");
							        http_response_code(400);
							        echo $response->error_respond_api();
							}
						}
						
						// if base64 image upload & generating respective responses
						if (!empty($data->base64_image)) {
							
							if(preg_match("/^data:image\/(?<extension>(?:png|gif|jpg|jpeg));base64,(?<image>.+)$/", $data->base64_image, $matchings))
							{
							   $imageData = base64_decode($matchings['image']);
							   $extension = $matchings['extension'];
							   $filepath=UPLOAD_DIR ."user/". uniqid().".%s";
							   $filename = sprintf($filepath, $extension);
							   if(file_put_contents($filename, $imageData))
							   {
							   	$profile_image=$filename;
							   }
							}else{
								$response->set_error_response(null, "invalid image format", "400","Image format not match must use .png, .gif, .jpg, .jpeg format");
							    http_response_code(400);
							    echo $response->error_respond_api();
							}
						}
							//set value of user object
							$user->set_user($merchat_id,$user_name,$email,$password,$first_name,$last_name,$phone_number,$address,$user_ion_id,$profile_image,$email_sending,$cradit_recharge);

							$create_user=$user->update_user($user_ion_id,$conn);
							if ($create_user===true) {
								try{
									$response->set_success_response(null, "user Updated successfully", "200","Data has been updated successfully");
								    http_response_code(200);
								    echo $response->success_respond_api();
								}catch (Exception $ex) {
									$response->set_error_response(null,$ex->getMessage(), "500","Server Problem");
				                    http_response_code(500);
				                    echo $response->error_respond_api();
								}

							}
	                }else{
	                    $response->set_error_response(null,$varify['error'], "500","Token expire please create new");
	                    http_response_code(500);
	                    echo $response->error_respond_api();
	                }
	            }else{
						$response->set_error_response(null, "Invalid user ID", "400","User not exist on this id");
				        http_response_code(400);
				        echo $response->error_respond_api();
					}				
			}catch(Exception $ex){
                $response->set_error_response(null,$ex->getMessage(), "500","internal server error");
                http_response_code(500);
                echo $response->error_respond_api();
	        }
	    }else{
	        $response->set_error_response(null,"Auth key required", "404","Auth key required");
	        http_response_code(404);
	        echo $response->error_respond_api();
	    }

}
