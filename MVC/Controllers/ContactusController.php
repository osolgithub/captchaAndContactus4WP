<?php
/**
* \class OSOLCCC::Controllers::ContactusController

*  \brief \OSOLCCC\Controllers\ContactusController: Used for handling Contact us functionality
*  \details  
 This class is the controller class for contact us module. \n
\par Doxygen Tip
Use this regexp to get methods
```
([^\}\r\n\/\$]+)function([^\(\r\n]+)\(
```
Use named anchors where required
```
<a name="git_html_pages"></a>
```
\par instantiation 
\OSOLCCC\Controllers\ContactusController::getInstance()\n

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
*

*  @date 23rd June 2022

 */
 
 
 
namespace OSOLCCC\Controllers;
class ContactusController extends \OSOLCCC\SingletonParent{
	/**
     *  @brief Validates Contact us form Submission
     *
     *  @param [in] no input paramaeters
     *  @return array $validation_result
     *  @author Sreekanth Dayanand
     *  @date 23rd June 2022
     *  @details 
	   
		Checks $_REQUEST['action'], executed if value is either 'cust_captcha_contact_submit' or 'cccontact_validate_ajax'.@n
		
		\par Called in 
		
		1. \OSOLCCC\Hooks\Frontend->cust_captcha_contact_func() @n
		Which in turn is called from add_shortcode( 'cccontact', [$OSOLCCC_Frontend_inst,'cust_captcha_contact_func'] ); in custCaptchaContact.php
		
		2.  \OSOLCCC\Hooks\Frontend->cust_captcha_contact_validate_contact_ajax()@n
		Which in turn is called from add_action('wp_ajax_cccontact_validate_ajax', [$OSOLCCC_Frontend_inst,'cust_captcha_contact_validate_contact_ajax']); & add_action('wp_ajax_nopriv_cccontact_validate_ajax', [$OSOLCCC_Frontend_inst,'cust_captcha_contact_validate_contact_ajax']); in custCaptchaContact.php@n
		
		
		\par Uses Methods
		calls $this->cccontact_validate_notify_form(); for backend validation of 'subject','message','name' & 'email'
		
     */
	function cust_captcha_contact_validate_and_mail()
	{
		$validation_result = array();
		if((isset($_REQUEST['cccontact_action']) && $_REQUEST['cccontact_action']== 'cust_captcha_contact_submit') ||
			(isset($_REQUEST['action']) && $_REQUEST['action']== 'cccontact_validate_ajax')												  
															  )
		{
			//die("GGGG");
			$validation_result = $this->cccontact_validate_notify_form();
			if(count($validation_result) > 0)
			{
				//die($validation_result);
				if((!isset($_REQUEST['action']) || $_REQUEST['action'] != 'cccontact_validate_ajax')	)
				{
					echo "<center><h2>".__( implode("<br />",$validation_result), 'osolwpccc' )."!</h2></center>";
				}
				return $validation_result;
			}
																									
			if ( isset( $_REQUEST['OSOLmulticaptcha_keystring'] ) && "" ==  $_REQUEST['OSOLmulticaptcha_keystring'] ) {
				//wp_die( __( 'Please complete the CAPTCHA.', 'osolwpccc' ) );
				$captchaValidationFailMessage =  'Please complete the CAPTCHA.';
				if((!isset($_REQUEST['action']) || $_REQUEST['action'] != 'cccontact_validate_ajax')	)
				{
					echo "<center><h2>".__($captchaValidationFailMessage, 'osolwpccc' )."!</h2></center>";
				}
				else
				{
					$validation_result[] = $captchaValidationFailMessage;
				}
				return $validation_result;
			}
			if(!session_id()){
				session_start();
			}
			// Check entered captcha
			/*if ( isset( $_REQUEST['OSOLmulticaptcha_keystring'] ) && ($_SESSION['OSOLmulticaptcha_keystring'] == $_REQUEST['OSOLmulticaptcha_keystring'] )) {
				return;
			} else {*/
			
			if(get_option('cust_captcha_status') == 'enabled' && (!isset($_REQUEST['OSOLmulticaptcha_keystring']) || !\OSOLCCC\Helpers\ContactusHelper::getInstance()->verifyOSOLMultiCaptcha()))
			{
				//die($_REQUEST['OSOLmulticaptcha_keystring']."<pre>".print_r($_SESSION['OSOLmulticaptcha_keystring'],true)."</pre>");
				//wp_die( __( 'Error: Incorrect CAPTCHA. Press your browser\'s back button and try again.', 'osolwpccc' ) );
				$captchaValidationFailMessage =  'Error: Incorrect CAPTCHA. ';
				if((!isset($_REQUEST['action']) || $_REQUEST['action'] != 'cccontact_validate_ajax')	)
				{
					echo "<center><h2>".__($captchaValidationFailMessage."Press your browser's back button and try again.", 'osolwpccc' )."!</h2></center>";
				}
				else
				{
					$validation_result[] = $captchaValidationFailMessage;
				}
				return $validation_result;
			}
			
			$sendMailResult = \OSOLCCC\Helpers\CommonClass::getInstance()->sendMail();
			return $sendMailResult;
			
				
		}
		return $validation_result;//return true;
	}//function cust_captcha_contact_validate_and_mail()
	
	/**
     *  @brief Validates 'subject','message','name' & 'email' in  Contact us form
     *
     *  @param [in] no input paramaeters
     *  @return array $validation_result
     *  @author Sreekanth Dayanand
     *  @date 23rd June 2022
     *  @details 
		Called in $this->cust_captcha_contact_validate_and_mail().
		
		
     */
	function cccontact_validate_notify_form()
	{
		$validation_result = array();
		$input_fields = array('subject','message','name','email');
		foreach($input_fields as $field_name)
		{
			if(!isset($_REQUEST["cccontact_".$field_name]) || (trim($_REQUEST["cccontact_".$field_name]) == ''))
			{
				$validation_result[] = $field_name ." should not be blank <br />";
			}
			
		}
			$emailFilter = "/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/";
			if(!preg_match($emailFilter,$_REQUEST['cccontact_email']))
			{
				$validation_result[] = "Please enter a valid from email <br />";
			}
			
			$phoneFilter = "/^([0-9_\.\-\s])+$/";
			if(isset($_REQUEST['cccontact_phone'])  && !preg_match($phoneFilter,$_REQUEST['cccontact_phone']))
			{
				$validation_result[] = "Please enter a valid phone number. " . $_REQUEST['cccontact_phone']. " is incorrect";
			}
		return $validation_result;//implode("<br />",$validation_result);
		
	}
}//Class Contactus extends \OSOLCCC\SingletonParent{
?>