<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-type:application/json; charset=utf-8");

require_once '../../../config/connection/connection.php';
require_once '../../../vendor/autoload.php'; 
require_once '../../model/user/user.php';
require_once '../../api_response/response.php';
include_once '../../helpers/validator.php';
include_once '../../helpers/varification.php';
include_once '../../helpers/mail.php';
include_once '../../language/language.php';


$response= new Response();
    if ($_SERVER['REQUEST_METHOD']=== "POST") {
    $data = json_decode(file_get_contents('php://input'));

        // object creating
        $db=new DataBase();
        $conn = $db->get_connection();
        $user = new User();
        $validator = new Validator();
        $varification = new Varification();
       
        // Get Auth key for varification
        $all_headers=getallheaders();
        $jwt_key=$all_headers['Auth_key'];
        if (!empty($jwt_key)) {
            try{
                $varify=$varification->get_user_data($jwt_key);
                if(!empty($varify['decoded_data'])){
                    $user_ion_id=$varify['decoded_data']->data->user_ion_id;
                    if (!empty($user_ion_id)) {
                        
                        $get_user=$user->find_user("secondary_user",'user_ion_id',$user_ion_id,$conn);
                        if(!empty($get_user)){
                            $merchant_id=$get_user['merchant_id'];
                            $user_name=$get_user['user_name'];
                            $email=$get_user['email'];
                            $first_name=$get_user['first_name'];
                            $last_name=$get_user['last_name'];
                            $phone_number=$get_user['phone_number'];
                            $address=$get_user['address'];
                            $profile_image=$get_user['profile_image'];
                            $password=$get_user['password'];
                            $email_sending=$get_user['email_sending'];
                            $cradit_recharge=$get_user['cradit_recharge'];
                            $user_ion_id=$user_ion_id;              

                            //validating data & generating respective responses
                                
                                //validating user name & generating respective responses
                                if (!empty($data->user_name)) {
                                    if ($validator->contain_non_alpha_numaric($data->user_name)===0) {
                                            $response->set_error_response(null, "only alphabets and numaric values allowed in user name", "400","only use alphabets and number like abc,Abc,123abc,abc123");
                                            echo $response->error_respond_api();
                                    }else
                                        $user_name=$data->user_name;
                                }

                                if (!empty($data->email)) {
                                    if ($validator->validate_email($data->email) === false) {
                                        $response->set_error_response(null,"invalid email format", "400","use proper mail like example@example.com");
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
                                            echo $response->respond_api();
                                        }else
                                            $password=password_hash($data->password, PASSWORD_DEFAULT);
                                    }else{
                                            $response->set_error_response(null, "Password and confirm password not match", "400","passwrod and confirm password not match");
                                            echo $response->error_respond_api();
                                        }
                                }

                                //validating first name format & generating respective responses
                                if (!empty($data->first_name)) {
                                     if ($validator->contain_non_alphabet($data->first_name)) {
                                            $response->set_error_response(null, "only alphabets allowed in first name", "400","use proper formate for that Abc,abc,aBc ");
                                            echo $response->error_respond_api();
                                    }else
                                        $first_name=$data->first_name;
                                }
                            
                                //validating last name format & generating respective responses
                                if (!empty($data->last_name)) {
                                     if ($validator->contain_non_alphabet($data->last_name)) {
                                            $response->set_error_response(null, "only alphabets allowed in last name", "400","use proper formate for that Abc,abc,aBc");
                                            echo $response->error_respond_api();
                                    }else
                                        $last_name=$data->last_name;
                                }
                            
                                //validating contact number & generating respective responses
                                if (!empty($data->phone_number)) {
                                     if ($validator->contain_non_integer($data->phone_number)) {
                                           $response->set_error_response(null,"only integers allowed in phone number","400","use proper formate for that 03********* ");
                                            echo $response->error_respond_api();
                                    }else
                                        $phone_number=$data->phone_number;
                                }

                                // if base64 image upload & generating respective responses
                                if (!empty($data->base64_image)) {
                                    if(preg_match("/^data:image\/(?<extension>(?:png|gif|jpg|jpeg));base64,(?<image>.+)$/", $data->base64_image, $matchings))
                                    {
                                        
                                        $save_image=$user->save_image('secondary_user',$matchings);
                                        if (isset($save_image)) {
                                             
                                             $profile_image=$save_image;

                                        }
                                    }else{
                                        $response->set_error_response(null, "invalid image format", "400","Image format not match must use .png, .gif, .jpg, .jpeg format");
                                        echo $response->error_respond_api();
                                    }
                                }

                            $data->email_sending=1 ? $email_sending=$data->email_sending : $email_sending;
                            $data->cradit_recharge = 1 ? $cradit_recharge=$data->cradit_recharge : $cradit_recharge;

                            
                            // $cradit_recharge=$data->cradit_recharge;

                            $user->set_user($user_name,$email,$password,$first_name,$last_name,$phone_number,$address,$user_ion_id,$profile_image,$email_sending,$cradit_recharge,$merchant_id);
                            
                            // try update user or give exception
                            try{
                                $user->update_profile("secondary_user",$conn);
                                $response->set_success_response(null, "updated successfully", "200","User has been updated successfully");
                                    echo $response->success_respond_api();
                            }catch (Exception $ex) {

                                $response->set_error_response(null,$es->getMessage(), "500","Something Went Wrong");
                                echo $response->error_respond_api();
                            } 
                        }else{
                            $response->set_error_response(null,"Invalid user id", "404","Please enter a valid id");
                            echo $response->error_respond_api();
                        }
                    }else{
                        $response->set_error_response(null,"user id required", "404","user id field empty");
                        echo $response->error_respond_api();
                    }
                }else{

                    $response->set_error_response(null,$varify['error'], "500","Something Went Wrong");
                    echo $response->error_respond_api();
                }
            }catch(Exception $ex){
                    $response->set_error_response(null,$ex->getMessage(), "500","Something Went Wrong");
                    http_response_code(500);
                    echo $response->error_respond_api();
            }
        }else{
            $response->set_error_response(null,"Auth key required", "404","Auth key required");
            echo $response->error_respond_api();
        }
    }else{
            $response->set_error_response(null,"Request not Accept", "404","Use post request to send data");
            echo $response->error_respond_api();
    }
