<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-type:application/json; charset=utf-8");

require_once '../../../config/connection/connection.php';
require_once '../../../vendor/autoload.php'; 
require_once '../../model/user/merchant.php';
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
        $user = new Merchant();
        $validator = new Validator();
        $varification = new Varification();
        $mail = new Mail();
        // Get Auth key for varification
        $all_headers=getallheaders();
        $jwt_key=$all_headers['Auth_key'];
        if (!empty($jwt_key)) {
            if (!empty($data->merchant_id)) {
                
                $get_user=$user->check_user($data->merchant_id,$conn);
                    
                if(!empty($get_user)){
                  try{  
                    $varify=$varification->get_user_data($jwt_key);
                    if(!empty($varify['decoded_data'])){
                        $merchant_id=$data->merchant_id;

                        $number_recipients=0;
                        //validating data & generating respective responses
                            
                            //validating email format & generating respective responses
                            if (!empty($data->from)) {
                              foreach ($data->from as $key => $value) {
                                if ($validator->validate_email($value) === false) {
                                      $response->set_error_response(null,"invalid format", "400","use proper mail like example@example.com");
                                      http_response_code(400);
                                      echo $response->error_respond_api();
                                }else{
                                  $from_email=$value;
                                  $from_name=$key;
                                }
                              }
                            }
                            //validating email format & generating respective responses
                            if (!empty($data->to)) { 
                              foreach ($data->to as $key=> $value) {
                                if ($validator->validate_email($value->email) === false) {
                                        $response->set_error_response(null,"invalid format", "400","use proper mail like example@example.com of User");
                                        http_response_code(400);
                                        echo $response->error_respond_api();
                                  }else{
                                    $to_email_array[] = $value->email;
                                    $number_recipients++;
                                  }
                                }                              
                                $to_email=implode(',', $to_email_array);
                              }
                            //validating email format & generating respective responses
                            if (!empty($data->cc)) { 
                              foreach ($data->cc as $key=> $value) {
                                if ($validator->validate_email($value->email) === false) {
                                        $response->set_error_response(null,"invalid format", "400","use proper mail like example@example.com of User");
                                        http_response_code(400);
                                        echo $response->error_respond_api();
                                  }else{
                                    $cc_email_array[] = $value->email;
                                    $number_recipients++;
                                  }
                                }                              
                                $cc_email=implode(',', $cc_email_array);
                              }
                            
                            //validating email format & generating respective responses
                            if (!empty($data->bcc)) { 
                              foreach ($data->bcc as $key=> $value) {
                                if ($validator->validate_email($value->email) === false) {
                                        $response->set_error_response(null,"invalid format", "400","use proper mail like example@example.com of User");
                                        http_response_code(400);
                                        echo $response->error_respond_api();
                                  }else{
                                    $bcc_email_array[] = $value->email;
                                    $number_recipients++;
                                  }
                                }                              
                                $bcc_email=implode(',', $bcc_email_array);
                              }
                            
                            //validating first subject format & generating respective responses
                            if (empty($data->subject)) {
                              $response->set_error_response(null, "Subject required", "400","please ender some value in subject field");
                              http_response_code(400);
                              echo $response->error_respond_api();
                            }
                            //validating first subject format & generating respective responses
                            if (empty($data->body)) {
                              $response->set_error_response(null, "Body required", "400","please ender some value in body field");
                              http_response_code(400);
                              echo $response->error_respond_api();
                            }    

                          // set mail properties
                            $request_id=uniqid();
                          $mail->set_param($from_name,$from_email,$to_email,$cc_email,$bcc_email,$data->subject, $data->body,$request_id,$merchant_id);
                            try {
                                // check balance 
                                $cradit_data=$user->get_user_cradit($data->merchant_id,$conn);
                                if(!empty($cradit_data)){
                                  $total_charges=$number_recipients*0.0489;
                                  if ($cradit_data['credit'] >=$total_charges ) {
                                    
                                    // send mail    
                                    // $mail->send_mail();
                                    $remaining_cradit=$cradit_data['credit']-$total_charges;
                                    // charge amount
                                    $user->charge_amount($data->merchant_id,$remaining_cradit,$conn);
                                
                                    //save mail data in Datebase
                                    $request_id=$mail->save_mail("send",$conn);

                                    //Set response properties
                                    $response->set_success_response(null, "Mail send successfully", "200","Mail send successfully");
                                    
                                    // save response in database
                                    $response->save_respond_request($request_id,$conn);
                                    
                                    // set and print the response
                                    http_response_code(200);
                                    echo $response->success_respond_api();
                                  }else{
                                        //save mail data in Datebase
                                    $request_id=$mail->save_mail("not send",$conn);
                                    // save response properties
                                    $response->set_error_response(null,"insufficient cradit", "404","Please recharge your account");

                                    // save response in database
                                    $response->save_respond_request($request_id,$conn);
                                    
                                    // set and print the response
                                    http_response_code(404);
                                    echo $response->error_respond_api();
                                  }
                                }else{
                                      //save mail data in Datebase
                                  $request_id=$mail->save_mail("not send",$conn);
                                  // save response properties
                                  $response->set_error_response(null,"insufficient cradit", "404","Please recharge your account");

                                  // save response in database
                                  $response->save_respond_request($request_id,$conn);
                                  
                                  // set and print the response
                                  http_response_code(404);
                                  echo $response->error_respond_api();
                                }
                            } catch (Exception $e) {

                              //save mail data in Datebase
                              $request_id=$mail->save_mail("not send",$conn);
                              // save response properties
                              $response->set_error_response(null,$e->getMessage(), "500","something went worng");

                              // save response in database
                              $response->save_respond_request($request_id,$conn);
                              
                              // set and print the response
                              http_response_code(500);
                              echo $response->error_respond_api();
                            }
                    }else{
                        $response->set_error_response(null,$varify['error'], "500","Invaid Auth key please create another one");
                        http_response_code(500);
                        echo $response->error_respond_api();
                    }
                  }catch (Exception $ex) {
                    $response->set_error_response(null,$ex->getMessage(), "500","something went worng");
                    http_response_code(500);
                    echo $response->error_respond_api();
                  } 
                }else{
                    $response->set_error_response(null,"Invalid user id", "404","Please enter a valid id");
                    http_response_code(404);
                    echo $response->error_respond_api();
                }
            }else{
                    $response->set_error_response(null,"user id required", "404","user id field empty");
                    http_response_code(404);
                    echo $response->error_respond_api();
                }
        }else{
            $response->set_error_response(null,"Auth key required", "404","Auth key required");
            http_response_code(404);
            echo $response->error_respond_api();
        }


}

