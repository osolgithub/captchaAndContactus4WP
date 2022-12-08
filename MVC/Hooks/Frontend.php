<?php
/**
* @class OSOLCCC::Hooks::Frontend
@brief OSOL_CCC_Handler class: Used for OOPfying the frontend operations of wp plugin
*  @details  
 This class encapsulates all custom methods for for frontend of the plugin. \n
 This class also  encapsulates all frontend hooks for the plugin. \n
 - This class deals with
	1. display captcha 
	2. generate captcha html for wordpress forms (login, register, lost password, post, comment)
	2. validate captcha on submission
	3. display contact us form
	4. process contact us form submission 
instantiation 
\OSOLCCC\Hooks\Frontend::getInstance() 
	

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
*
*/

namespace OSOLCCC\Hooks;
defined('CUST_CAPTCHA_FOLDER') or die('Direct access not permitted');
class Frontend extends \OSOLCCC\SingletonParent{
	
	
	//declare variables pertinant to the functionality
	
	
	protected function __construct(){
		//echo "initialized \OSOLCCCF\Helpers\Frontend()!!!";
		
	}
	
	
	//wordpress hooks methods for frontend starts here
	
	function init4Frontend(){
		if(!$GLOBALS['OSOLMulticaptcha_gdprCompliantNoCookie'])
		{
			if(!session_id()){
				session_start();
			}
		}
		
		//if(is_admin())add_thickbox(); // thickbox wont load in admin via wp_enque_scripts
	}//function init4FrontEnd(){
	
	/* <!-- Captcha for login authentication starts here --> */
	/* Function to include captcha for login form */
	function include_cust_captcha_login(){
		\OSOLCCC\Hooks\Common::getInstance()->add_ccc_onload();//in login pages, hooks ie "add_action( 'admin_footer', & add_action( 'wp_footer'," don't work since there is no  <footer>  
		
		echo \OSOLCCC\Views\ContactusView::getInstance()->getCaptchaBlockHTML('comment');		
		/* Will retrieve the get varibale and prints a message from url if the captcha is wrong */
		if(isset($_GET['captcha']) && $_GET['captcha'] == 'confirm_error' ) {
			if(isset($_SESSION['captcha_error']))
			{			
				echo '<label style="color:#FF0000;" id="cust_capt_err" for="cust_captcha_code_error">'.$_SESSION['captcha_error'].'</label><div style="clear:both;"></div>';;
				$_SESSION['captcha_error'] = '';
			}
		}
		return true;
	}//function include_cust_captcha_login(){
	
	
	/* Hook to find out the errors while logging in */
	function cust_captcha_login_errors($errors){
		//die('die statement executed at function cust_captcha_login_errors IN '.__FILE__);
		if( isset( $_REQUEST['action'] ) && 'register' == $_REQUEST['action'] )
			return($errors);
		
		if(get_option('cust_captcha_status') == 'enabled' &&  !\OSOLCCC\Helpers\ContactusHelper::getInstance()->verifyOSOLMultiCaptcha()){
			return $errors.'<label id="capt_err" for="cust_captcha_code_error">'.__('Captcha confirmation error!', 'osolwpccc').'</label>';
		}
		return $errors;
	}

	/* Hook to redirect after captcha confirmation */
	function include_cust_captcha_login_redirect($url){
		
		/* Captcha mismatch */
		if(get_option('cust_captcha_status') == 'enabled' && (!isset($_REQUEST['OSOLmulticaptcha_keystring']) || !\OSOLCCC\Helpers\ContactusHelper::getInstance()->verifyOSOLMultiCaptcha())){
		//if(isset($_SESSION['captcha_code']) && isset($_REQUEST['captcha_code']) && $_SESSION['captcha_code'] != $_REQUEST['captcha_code']){
			if(isset($_SESSION['captcha_error']))$_SESSION['captcha_error'] = __('Incorrect captcha confirmation!', 'osolwpccc');
			wp_clear_auth_cookie();
			return $_SERVER["REQUEST_URI"]."/?captcha='confirm_error'";
		}
		/* Captcha match: take to the admin panel */
		else{
			return home_url('/wp-admin/');	
		}
	}

	/* <!-- Captcha for login authentication ends here --> */
	
	
	
	/* <!-- Captcha for comments starts here --> */
	/* Function to include captcha for comments form */
	function include_cust_captcha_comment_form(){//add_action
		/*$c_registered = get_option('wpcaptcha_registered');
		if ( is_user_logged_in() && $c_registered == 'yes') {
			return true;
		}*/
		
		echo \OSOLCCC\Views\ContactusView::getInstance()->getCaptchaBlockHTML('comment');
			
		return true;
	}
	
	/* Function to include captcha for comments form > wp3 */
	function include_cust_captcha_comment_form_wp3(){
		/*$c_registered = get_option('wpcaptcha_registered');
		if ( is_user_logged_in() && $c_registered == 'yes') {
			return true;
		}*/
		
		
		echo \OSOLCCC\Views\ContactusView::getInstance()->getCaptchaBlockHTML('comment');
			
		remove_action( 'comment_form', 'include_cust_captcha_comment_form' );
		
		return true;
	}	
	// this function checks captcha posted with the comment
	function include_cust_captcha_comment_post($comment) {	
		/*$c_registered = get_option('wpcaptcha_registered');
		if (is_user_logged_in() && $c_registered == 'yes') {
			return $comment;
		}*/
		
		

		// skip captcha for comment replies from the admin menu
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'replyto-comment' &&
		( check_ajax_referer( 'replyto-comment', '_ajax_nonce', false ) || check_ajax_referer( 'replyto-comment', '_ajax_nonce-replyto-comment', false ) ) ) {
			// skip capthca
			return $comment;
		}

		// Skip captcha for trackback or pingback
		if ( $comment['comment_type'] != '' && $comment['comment_type'] != 'comment' ) {
			 // skip captcha
			 return $comment;
		}
		
		// If captcha is empty
		if(empty($_REQUEST['OSOLmulticaptcha_keystring']))
			wp_die( __('CAPTCHA cannot be empty.', 'osolwpccc' ) );

		// captcha was matched
		if($_SESSION['OSOLmulticaptcha_keystring'] == $_REQUEST['OSOLmulticaptcha_keystring']) return($comment);
		elseif(get_option('cust_captcha_status') == 'enabled' && (!isset($_REQUEST['OSOLmulticaptcha_keystring']) || !\OSOLCCC\Helpers\ContactusHelper::getInstance()->verifyOSOLMultiCaptcha()))
		{
			wp_die( __('Error: Incorrect CAPTCHA. Press your browser\'s back button and try again.', 'osolwpccc'));
		}
	} 
	/* <!-- Captcha for comments ends here --> */
	
	
	
	/* Function to include captcha for register form */
	function include_cust_captcha_register($default){
		
		echo \OSOLCCC\Views\ContactusView::getInstance()->getCaptchaBlockHTML('register');
		return true;
	}
	
	/* This function checks captcha posted with registration */
	function include_cust_captcha_register_post($login,$email,$errors) {

		// If captcha is blank - add error
		if ( isset( $_REQUEST['OSOLmulticaptcha_keystring'] ) && "" ==  $_REQUEST['OSOLmulticaptcha_keystring'] ) {
			$errors->add('captcha_blank', '<strong>'.__('ERROR', 'osolwpccc').'</strong>: '.__('Please complete the CAPTCHA.', 'osolwpccc'));
			return $errors;
		}

		/*if ( isset( $_REQUEST['OSOLmulticaptcha_keystring'] ) && ($_SESSION['OSOLmulticaptcha_keystring'] == $_REQUEST['OSOLmulticaptcha_keystring'] )) {
						// captcha was matched						
		} else */
		if(get_option('cust_captcha_status') == 'enabled' && (!isset($_REQUEST['OSOLmulticaptcha_keystring']) || !\OSOLCCC\Helpers\ContactusHelper::getInstance()->verifyOSOLMultiCaptcha()))
		{
			$errors->add('captcha_wrong', '<strong>'.__('ERROR', 'osolwpccc').'</strong>: '.__('That CAPTCHA was incorrect.', 'osolwpccc'));
		}
	  return($errors);
	} 
	/* End of the function include_cust_captcha_register_post */
	function include_cust_captcha_register_validate($results) {
		if ( isset( $_REQUEST['OSOLmulticaptcha_keystring'] ) && "" ==  $_REQUEST['OSOLmulticaptcha_keystring'] ) {
			$results['errors']->add('captcha_blank', '<strong>'.__('ERROR', 'osolwpccc').'</strong>: '.__('Please complete the CAPTCHA.', 'osolwpccc'));
			return $results;
		}

		/*if ( isset( $_REQUEST['OSOLmulticaptcha_keystring'] ) && ($_SESSION['OSOLmulticaptcha_keystring'] == $_REQUEST['OSOLmulticaptcha_keystring'] )) {
						// captcha was matched						
		} else*/ 
		if(get_option('cust_captcha_status') == 'enabled' && (!isset($_REQUEST['OSOLmulticaptcha_keystring']) || !\OSOLCCC\Helpers\ContactusHelper::getInstance()->verifyOSOLMultiCaptcha()))
		{
			$results['errors']->add('captcha_wrong', '<strong>'.__('ERROR', 'osolwpccc').'</strong>: '.__('That CAPTCHA was incorrect.', 'osolwpccc'));
		}
	  return($results);
	}
	/* End of the function include_cust_captcha_register_validate */
	/* <!-- Captcha for registration ends here --> */
	
	
	/* Function to include captcha for lost password form */
	function include_cust_captcha_lostpassword($default){
		
		echo \OSOLCCC\Views\ContactusView::getInstance()->getCaptchaBlockHTML('lost');
	}

	function include_cust_captcha_lostpassword_post() {
		if( isset( $_REQUEST['user_login'] ) && "" == $_REQUEST['user_login'] )
			return;

		// If captcha doesn't entered
		if ( isset( $_REQUEST['OSOLmulticaptcha_keystring'] ) && "" ==  $_REQUEST['OSOLmulticaptcha_keystring'] ) {
			wp_die( __( 'Please complete the CAPTCHA.', 'osolwpccc' ) );
		}
		
		// Check entered captcha
		/*if ( isset( $_REQUEST['OSOLmulticaptcha_keystring'] ) && ($_SESSION['OSOLmulticaptcha_keystring'] == $_REQUEST['OSOLmulticaptcha_keystring'] )) {
			return;
		} else {*/
		if(get_option('cust_captcha_status') == 'enabled' && (!isset($_REQUEST['OSOLmulticaptcha_keystring']) || !\OSOLCCC\Helpers\ContactusHelper::getInstance()->verifyOSOLMultiCaptcha()))
		{
			wp_die( __( 'Error: Incorrect CAPTCHA. Press your browser\'s back button and try again.', 'osolwpccc' ) );
		}
	}
	/* <!-- Captcha for lost password ends here --> */
	
	
	
	
	function cust_captcha_contact_func( $atts ) {
		extract( shortcode_atts( array(
			'toemail' => get_option('cust_captcha_contact_email'),
			'linktomodal' => 'no',
			'cccontact_unique_id' => '-1'
		), $atts ) );
		if(!$GLOBALS['OSOLMulticaptcha_gdprCompliantNoCookie'])
		{
			if(!session_id()){
				session_start();
			}
		}//if($OSOLMulticaptcha_gdprCompliantNoCookie) 
		$postid = get_the_ID();
		$toMailSessionVar = 'toemail-'.$postid."_".$cccontact_unique_id;
		$_SESSION[$toMailSessionVar] = $toemail;
		
		//ob_start();
		\OSOLCCC\Controllers\ContactusController::getInstance()->cust_captcha_contact_validate_and_mail();
		
		include(CUST_CAPTCHA_FOLDER.'/cccontact_form.php');
		//$output_string=ob_get_contents();
		//ob_end_clean();
		return $output_string;//"cccontact :".$toemail." , ".$linktomodal." ,$toMailSessionVar";
	}
	function cccontact_tb_show_modal()
	{
	//echo "HI";
		die( $this->cust_captcha_contact_func( 
			array()
		));

		
	}
	
	 function cust_captcha_contact_validate_contact_ajax() {
		//Handle request then generate response using WP_Ajax_Response
		$validation_result = \OSOLCCC\Controllers\ContactusController::getInstance()->cust_captcha_contact_validate_and_mail();
		//echo  "<pre>".print_r($validation_result,true)."</pre>";
			if(is_array($validation_result) && count($validation_result) > 0 )
			{
				//echo  "</pre>".print_r($_REQUEST,true)."</pre>";
				die("{\"success\":0,\"message\":\"".(join("\r\n",$validation_result))."\"}");
			}
			die("{\"success\":\"1\"}");
			return true;
	 }
	
	
	/* <!-- CONTACT US PART ends here --> */
	
	//wordpress hooks ends here
	
}//Class Frontend{





?>