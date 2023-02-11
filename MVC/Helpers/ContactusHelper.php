<?php
/**
* @class OSOLCCC::Helpers::ContactusHelper
*  @brief \OSOLCCC\ContactusHelper\ContactusHelper: Used for handling Contact us functionality
*  @details  
 This class is the helper class for contact us module. \n
 
\par instantiation 
\OSOLCCC\Helpers\ContactusHelper::getInstance() 
* @author
* Name: Sreekanth Dayanand, www.outsource-online.net
* Email: joomla@outsource-online.net
* Url: http://www.outsource-online.net
* ===================================================
* @copyright (C) 2012,2013 Sreekanth Dayanand, Outsource Online (www.outsource-online.net). All rights reserved.
* @license see http://www.gnu.org/licenses/gpl-2.0.html  GNU/GPL.
* You can use, redistribute this file and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation.
* If you use this software as a part of own sofware, you must leave copyright notices intact or add OSOLMulticaptcha copyright notices to own.

*  @date 23rd June 2022
 */
namespace OSOLCCC\Helpers;
class ContactusHelper extends \OSOLCCC\SingletonParent{
	function verifyOSOLMultiCaptcha()
	{
		//die("<pre>".print_r($_SESSION['OSOLmulticaptcha_keystring'],true)."</pre>");
		$verificationResult = false;
		if($GLOBALS['OSOLMulticaptcha_gdprCompliantNoCookie'])
		{
			$captcha = \OSOLCCC\ExtraClasses\OSOLmulticaptcha::getInstance();
			$captchaEncryptionKey = get_option('OSOLCaptchaEncryptionKey');// IMPORTANT ****** YOU MUST SET A CUSTOM VALUE FOR YOUR SITE from admin panel
			$captcha->setCaptchaEncryptionKey($captchaEncryptionKey);
			$captchaText2Check = isset($_POST['OSOLmulticaptcha_keystring'])?$_POST['OSOLmulticaptcha_keystring']:"";
			$encryptedCaptchaString = isset($_POST['OSOLmulticaptcha_captchaEncypted'])?$_POST['OSOLmulticaptcha_captchaEncypted']:"";
			
			$OSOLMulticaptcha_caseInsensitive = get_option('OSOLMulticaptcha_caseInsensitive');
			$caseInsensitiveCheck = ($OSOLMulticaptcha_caseInsensitive == 1);
			
			if(trim($captchaText2Check) != "" && ($verificationResult = $captcha->isCaptchaCorrect($captchaText2Check, $encryptedCaptchaString, $caseInsensitiveCheck)))
			{
				//chaptcha text entered is correct
			}
			else
			{
				//chaptcha text entered is not correct
			}
			
		}
		else
		{
			if($verificationResult = in_array((get_option('OSOLMulticaptcha_caseInsensitive')=='1'?strtoupper($_REQUEST['OSOLmulticaptcha_keystring']):$_REQUEST['OSOLmulticaptcha_keystring']),$_SESSION['OSOLmulticaptcha_keystring']))
			{
				//if verification success remove the session val of that captcha so that bots dont misuse it
				foreach($_SESSION['OSOLmulticaptcha_keystring'] as $key => $val)
				{
					if(strtoupper($_REQUEST['OSOLmulticaptcha_keystring']) == strtoupper($val))
					{
						unset($_SESSION['OSOLmulticaptcha_keystring'][$key]);
					}
				}
			}
		}//if($GLOBALS['OSOLMulticaptcha_gdprCompliantNoCookie']) 
		
		return $verificationResult;
	}//function verifyOSOLMultiCaptcha()
	/**
     *  @brief sendMail : called in \OSOLCCC\Controllers\ContactusController->cust_captcha_contact_validate_and_mail() 
     *
     *  @param [in] no input paramaeters
     *  @return array $validation_result
     *  @author Sreekanth Dayanand
     *  @date 23rd June 2022
     *  @details 
		Validates 'subject','message','name' & 'email' in  Contact us form@n
		@see \OSOLCCC\Controllers\ContactusController->cust_captcha_contact_validate_and_mail()\n
		@ref \OSOLCCC\Controllers\ContactusController::cust_captcha_contact_validate_and_mail()
		
		
     */
	function sendMail()
	{
			$validation_result = array();
			$toMailSessionVar = $_REQUEST['cccontact_toMailSessionVar'];
			$toemail = isset($_SESSION[$toMailSessionVar])?$_SESSION[$toMailSessionVar]:get_option('cust_captcha_contact_email');
			//die($toMailSessionVar." : " .$_SESSION[$toMailSessionVar]. " : " .$toemail);
				$messagePrepend = "This is a mail send from ".get_bloginfo( 'wpurl' )." .\n";
				$messageAppend = "<br />\r\n<br />\r\n<br />\r\nMessage send via wordpress plugin https://wordpress.org/plugins/customizable-captcha-and-contact-us-form/ \n";
				
				$summary =	$messagePrepend ."<br />\r\n Phone #:{$_REQUEST['cccontact_phone']}<br />\r\n Message:<br />\r\n".nl2br($_REQUEST['cccontact_message']). $messageAppend;
			
				$subject = $_REQUEST['cccontact_subject']." (mail from ".get_bloginfo( 'name' )." )";
				
				$boundary = uniqid('np');
				
				 //$message = "This is a MIME encoded message."; 
				 $message = 'This message was sent via '.get_bloginfo( 'name' ).','.get_bloginfo( 'wpurl' ).' !' ;
						   
				 $message .= "\r\n\r\n--" . $boundary . "\r\n";
				 $message .= "Content-type: text/plain;charset=utf-8\r\n\r\n";
				 $message .= strip_tags($summary) . "\r\n\r\n" ;
				
				 $message .= "\r\n\r\n--" . $boundary . "\r\n";
				 $message .= "Content-type: text/html;charset=utf-8\r\n\r\n";
				 $message .= "<br />".$summary . "<br />" . "<br />" ;
				
				 $message .= "\r\n\r\n--" . $boundary . "--";
				
				
				/*$message = 'This message was sent via PHP !' . "\r\n" .
						   $summary . "\r\n" . "\r\n" .
						   '<br />From <a href=\"'.$realestate_base.'\">'.$realestate_base."</a>". "\r\n";*/
				
				// To send HTML mail, the Content-type header must be set
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				//$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";
				
				// Additional headers
				$headers .= 'From: "'.$_REQUEST['cccontact_name'].'" <'.$_REQUEST['cccontact_email'].'>' . "\r\n" .
						   //'Cc: "Outsource Online Internet Solutions" <office@outsource-online.net>' . "\r\n" .
						   'X-Mailer: PHP-' . phpversion() . "\r\n";
				//die("$toemail, $subject, $message, $headers");
				$multiple_to_recipients = array(
												$toemail,
												/* '"Outsource Online" <office@outsource-online.net>', */
											);
				//if (@mail($toemail, $subject, $message, $headers)) {
				if(wp_mail($multiple_to_recipients, $subject, $message, $headers )){
				  //wp_die( __('mail sent Successfully!' . "\n"));//.'to '.$to
				   $mailSendStatus = "Thank you for contacting us!";
				   
					if((!isset($_REQUEST['action']) || $_REQUEST['action'] != 'cccontact_validate_ajax')	)
					{
						echo "<center><h2>".__($mailSendStatus, 'osolwpccc' )."!</h2></center>";
					}
					else
					{
						$validation_result[] = $mailSendStatus;
					}
				}
				else {
				  //wp_die( __('mail() Failure!.contact site admin' . "\n"));
				   //echo "<center><h2>".__("Can't send mail because of system failure!.contact site admin\n")."</h2></center>";return;
				   $mailSendStatus = "Not able to send mail because of system failure!.contact site admin";
				   
					if((!isset($_REQUEST['action']) || $_REQUEST['action'] != 'cccontact_validate_ajax')	)
					{
						echo "<center><h2>".__($mailSendStatus, 'osolwpccc' )."!</h2></center>";
					}
					else
					{
						$validation_result[] = $mailSendStatus;
					}
					
				}
				return $validation_result;
	}//function sendMail()
}//Class ContactusView extends \OSOLCCC\SingletonParent{
	?>