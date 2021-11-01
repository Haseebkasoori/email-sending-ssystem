<?php 

require_once '../../../vendor/autoload.php';

use\Firebase\JWT\JWT;	
	/**
	 * 
	 */

class Mail
{
        private $from_name;
        private $from_email;
        private $to_email;
        private $cc_name;
        private $cc_email;
        private $bcc_name;
        private $bcc_email;
        private $subject;
        private $body;
        private $merchant_id;
        private $sec_user_id;
        private $headers;
        private $otp;

        
    public function set_param($from_name="",$from_email="",$to_email,$cc_email="",$bcc_email="", $subject="",$body="",$headers="",$request_id="",$merchant_id="",$sec_user_id="",$otp="")
    {
        $this->from_name    = $from_name;
        $this->from_email   = $from_email;
        $this->to_email     = $to_email;
        $this->cc_email     = $cc_email;
        $this->bcc_email    = $bcc_email;
        $this->subject      = $subject;
        $this->body         = $body;   
        $this->request_id   =$request_id;   
        $this->merchant_id  =$merchant_id;
        $this->sec_user_id  =$sec_user_id;
        $this->headers      =$headers;
    }

    public function send_mail()
    {
        $to_email = $this->to_email;
        $body = $this->body;
        $subject = "Welcome to ESS";
        //creating Email Template for Token/OTP
        $headers = "MIME-Version: 1.0" . "\r\n";  
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";  
        $headers .= 'From:'.$this->from_name. "\r\n" .'Reply-To:'.$this->from_email. "\r\n". 'Cc:'.$this->cc_email."\r\n".'Bcc:'.$this->cc_email;
        if (mail($to_email, $subject, $body, $headers)) {
            return true;
        }else{
            return false;
        }
    }
    public function save_mail($status,$db_conn)
    {
        $from_name=$this->from_name;
        $from_email=$this->from_email;
        $to_email=$this->to_email;
        $cc_email=$this->cc_email;
        $bcc_email=$this->bcc_email;
        $subject=$this->subject;
        $body=$this->body;
        $request_id=$this->request_id;
        $merchant_id=$this->merchant_id;

        $query="INSERT INTO requests (from_name,from_email,to_email,cc_email,bcc_email,subject,body,request_id,merchant_id,status) VALUES (?,?,?,?,?,?,?,?,?,?)";
        $query=$db_conn->prepare($query);
        $query->bind_param("ssssssssss",$from_name,$from_email,$to_email,$cc_email,$bcc_email,$subject,$body,$request_id,$merchant_id,$status);
        $query->execute();
        $insert_id=$query->insert_id;
        if ($query->affected_rows >0){
            return $insert_id;
        }
        else{
            return false;
        }
        $db_conn->close();       

    }

    public function send_registration_mail($user_name,$user_ion_id,$password,$to_email){
        $body='<table border="0" width="430" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin:0 auto 0 auto" >
                <tbody>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            <p style="margin:10px 0 10px 0;color:#565a5c;font-size:18px">Hi '.$user_name.',</p>
                            <p style="margin:10px 0 10px 0;color:#565a5c;font-size:18px">We got a request for New Account Please use ID and password for login</p>
                        </td>
                    </tr>
                    <tr></tr>
                    <tr>
                        <td height="10" style="line-height:10px" colspan="1">&nbsp;</td>
                    </tr>
                    <tr>
                        <td><strong style="color:#ffff;text-decoration:none;display:block;width: 13em;text-align:center;background:#47a2ea;padding:0.5em;font-size:20px;margin-left: 4em; ">Your ID :  '.$user_ion_id.'
                            </strong></td>
                    </tr>
                    <tr>
                        <td><strong style="color:#ffff;text-decoration:none;display:block;width: 13em;text-align:center;background:#47a2ea;padding:0.5em;font-size:20px;margin-left: 4em;">Password :  '.$password.'
                            </strong></td>
                    </tr>
                    <tr>
                        <td height="10" style="line-height:10px" colspan="1">&nbsp;</td>
                    </tr>
                </tbody>
            </table>';
        //if data valid, create database object, get connection

        $to_email = $to_email;
        $subject = "Welcome to ESS";
        //creating Email Template for Token/OTP
        $headers = "MIME-Version: 1.0" . "\r\n";  
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";  
        $headers .= 'From: Localhost ESS' . "\r\n" .'Reply-To: webmaster@localhost.com';
       if (mail($to_email, $subject, $body, $headers)) {
          return true ;
        } else
            return false;

    } 
    
    public function send_forgot_mail($to_email,$user_ion_id,$otp)
    {

        $headers = "MIME-Version: 1.0" . "\r\n";  
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";  
        $headers .= 'From: Localhost ESS' . "\r\n" .'Reply-To: webmaster@localhost.com';
        $subject = "Forgot Password OTP  -- ".$otp." -- Localhost EMS";
        $body='<table border="0" width="430" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin:0 auto 0 auto" >
                    <tbody>
                        <tr>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <p style="margin:10px 0 10px 0;color:#565a5c;font-size:18px">Hi User,</p>
                                <p style="margin:10px 0 10px 0;color:#565a5c;font-size:18px">We got a request to reset your EMS password for ID: ('.$user_ion_id.'). <br>Here is your OTP</p>
                            </td>
                        </tr>
                        <tr></tr>
                        <tr>
                            <td height="10" style="line-height:10px" colspan="1">&nbsp;</td>
                        </tr>
                        <tr>
                            <td><strong style="color: #ffff;text-decoration:none;display:block;width: 10em;text-align: center;background: #47a2ea;padding: 1em;font-size: 20px;margin-left: 5em;">'.$otp.'
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

       if (mail($to_email, $subject, $body, $headers)) {
          return true ;
        } else
            return false;
    }

    public function reset_password_mail($to_email,$user_name,$user_ion_id,$password){

        $body='<table border="0" width="430" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin:0 auto 0 auto" >
                  <tbody>
                      <tr>
                          <td></td>
                      </tr>
                      <tr>
                          <td>
                              <p style="margin:10px 0 10px 0;color:#565a5c;font-size:18px">Hi '.$user_name.',</p>
                              <p style="margin:10px 0 10px 0;color:#565a5c;font-size:18px">Successfully rest your password for ID: '.$user_ion_id.' <br>Your new password is</p>
                          </td>
                      </tr>
                      <tr></tr>
                      <tr>
                          <td height="10" style="line-height:10px" colspan="1">&nbsp;</td>
                      </tr>
                      <tr>
                          <td><strong style="color:#ffff;text-decoration:none;display:block;width: 13em;text-align:center;background:#47a2ea;padding:0.5em;font-size:20px;margin-left: 4em; ">Your New Password :  '.$password.'
                              </strong></td>
                      </tr>
                      <tr>
                          <td height="10" style="line-height:10px" colspan="1">&nbsp;</td>
                      </tr>
                  </tbody>
              </table>';
            //if data valid, create database object, get connection
  
        $subject = "Password Changed";
        //creating Email Template for Token/OTP
        $headers = "MIME-Version: 1.0" . "\r\n";  
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";  
        $headers .= 'From: Localhost ESS' . "\r\n" .'Reply-To: webmaster@localhost.com';
        if (mail($to_email, $subject, $body, $headers)) {
              return true;
          }else{
              return true;
          }
    }
    
}