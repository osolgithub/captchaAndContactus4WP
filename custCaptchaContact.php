<?php
/*
Plugin Name: Customizable Captcha and contact us plugin
Plugin URI: http://www.outsource-online.net/
Description: Plugin to add captch to core wordpress forms and additional option for contact us page. From Sreekanth Dayanand(Outsource Online Internet Solutions)
Version:  1.0
Author: Sreekanth Dayanand
Author URI:http://www.outsource-online.net/


    Copyright 2013  Sreekanth Dayanand(Outsource Online Internet Solutions)  (email : wordpress@outsource-online.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/
define('CUST_CAPTCHA_FOLDER',dirname(__FILE__));
define('CUST_CAPTCHA_DIR_URL', plugin_dir_url(__FILE__));
/* Hook to initalize the admin menu */
add_action('admin_menu', 'cust_captcha_contact_plugin_menu');
/* Hook to initialize sessions */
add_action('init', 'cust_captcha_init_sessions');

/* Hook to store the plugin status */
register_activation_hook(__FILE__, 'cust_captcha_enabled');
register_deactivation_hook(__FILE__, 'cust_captcha_disabled');

function cust_captcha_contact_plugin_menu() {
	add_options_page('Customiazable Captcha and contact us plugin:Captcha settings', 'Captcha Settings', 'manage_options', 'cust-captcha-settings', 'cust_captcha_settings');
	
	add_options_page('Customiazable Captcha and contact us plugin:Contact page settings', 'Contact page settings', 'manage_options', 'cust-contact-settings', 'cust_captcha_contact_settings');
	

	
}
function cust_captcha_init_sessions(){
	if(!session_id()){
		session_start();
	}
	load_plugin_textdomain('wpcaptchadomain', false, dirname( plugin_basename(__FILE__)).'/languages');
	
	
}
function cust_captcha_enabled(){
	update_option('cust_captcha_status', 'enabled');
	if(get_option('cust_captcha_contact_email') == '')
	{
		update_option('cust_captcha_contact_email',get_option('admin_email'));
	}
}
function cust_captcha_disabled(){
	update_option('cust_captcha_status', 'disabled');
}
function cust_catcha_html()
{
	return '<img src="'.get_bloginfo('wpurl') .'/wp-load.php?show_cust_captcha=true&rand='.rand().'" />';
}
/* Captcha for login authentication starts here */ 

$login_captcha = get_option('cust_captcha_login');
//if($login_captcha == 'yes')
if(get_option('cust_captcha_status') ==  'enabled')
{
	add_action('login_form', 'include_cust_captcha_login');
	add_filter('login_errors','cust_captcha_login_errors');


	add_filter( 'login_redirect', 'include_cust_captcha_login_redirect', 10, 3 );	
}
/* Function to include captcha for login form */
function include_cust_captcha_login(){
	echo '<p class="login-form-captcha">
			<label for="captcha"><b>'. __('Captcha', 'wpcaptchadomain').' </b></label>
			<span class="required">*</span>
			<div style="clear:both;"></div>
			'.cust_catcha_html().'
			<div style="clear:both;"></div>';
			
	/* Will retrieve the get varibale and prints a message from url if the captcha is wrong */
	if(isset($_GET['captcha']) && $_GET['captcha'] == 'confirm_error' ) {
		echo '<label style="color:#FF0000;" id="cust_capt_err" for="cust_captcha_code_error">'.$_SESSION['captcha_error'].'</label><div style="clear:both;"></div>';;
		$_SESSION['captcha_error'] = '';
	}
	
	echo '<label for="OSOLmulticaptcha_keystring">'.__('Type the text displayed above', 'wpcaptchadomain').':</label>
			<input id="OSOLmulticaptcha_keystring" name="OSOLmulticaptcha_keystring" size="15" type="text" tabindex="30" />
			</p>';
	return true;
}

/* Hook to find out the errors while logging in */
function cust_captcha_login_errors($errors){
	//die('die statement executed at function cust_captcha_login_errors IN '.__FILE__);
	if( isset( $_REQUEST['action'] ) && 'register' == $_REQUEST['action'] )
		return($errors);
	
	if(get_option('cust_captcha_status') == 'enabled' && $_SESSION['OSOLmulticaptcha_keystring'] != $_REQUEST['OSOLmulticaptcha_keystring']){
		return $errors.'<label id="capt_err" for="cust_captcha_code_error">'.__('Captcha confirmation error!', 'wpcaptchadomain').'</label>';
	}
	return $errors;
}

/* Hook to redirect after captcha confirmation */
function include_cust_captcha_login_redirect($url){
	
	/* Captcha mismatch */
	if(get_option('cust_captcha_status') == 'enabled' && (!isset($_REQUEST['OSOLmulticaptcha_keystring']) || $_SESSION['OSOLmulticaptcha_keystring'] != $_REQUEST['OSOLmulticaptcha_keystring'])){
	//if(isset($_SESSION['captcha_code']) && isset($_REQUEST['captcha_code']) && $_SESSION['captcha_code'] != $_REQUEST['captcha_code']){
		$_SESSION['captcha_error'] = __('Incorrect captcha confirmation!', 'wpcaptchadomain');
		wp_clear_auth_cookie();
		return $_SERVER["REQUEST_URI"]."/?captcha='confirm_error'";
	}
	/* Captcha match: take to the admin panel */
	else{
		return home_url('/wp-admin/');	
	}
}

/* <!-- Captcha for login authentication ends here --> */



/* Captcha for Comments ends here */
$comment_captcha = get_option('cust_captcha_comments');
//if($comment_captcha == 'yes'){
if(get_option('cust_captcha_status') ==  'enabled')
{
	global $wp_version;
	if( version_compare($wp_version,'3','>=') ) { // wp 3.0 +
		add_action( 'comment_form_after_fields', 'include_cust_captcha_comment_form_wp3', 1 );
		add_action( 'comment_form_logged_in_after', 'include_cust_captcha_comment_form_wp3', 1 );
	}	
	// for WP before WP 3.0
	add_action( 'comment_form', 'include_cust_captcha_comment_form' );	
	add_filter( 'preprocess_comment', 'include_cust_captcha_comment_post' );
}

/* Function to include captcha for comments form */
function include_cust_captcha_comment_form(){
	/*$c_registered = get_option('wpcaptcha_registered');
	if ( is_user_logged_in() && $c_registered == 'yes') {
		return true;
	}*/
	echo '<p class="comment-form-captcha">
		<label for="captcha"><b>'. __('Captcha', 'wpcaptchadomain').' </b></label>
		<span class="required">*</span>
		<div style="clear:both;"></div>
		'.cust_catcha_html().'
		<div style="clear:both;"></div>
		<label for="OSOLmulticaptcha_keystring">'.__('Type the text displayed above', 'wpcaptchadomain').':</label>
		<input id="OSOLmulticaptcha_keystring" name="OSOLmulticaptcha_keystring" size="15" type="text" />
		<div style="clear:both;"></div>
		</p>';
	return true;
}

/* Function to include captcha for comments form > wp3 */
function include_cust_captcha_comment_form_wp3(){
	/*$c_registered = get_option('wpcaptcha_registered');
	if ( is_user_logged_in() && $c_registered == 'yes') {
		return true;
	}*/
	
	echo '<p class="comment-form-captcha">
		<label for="captcha"><b>'. __('Captcha', 'wpcaptchadomain').' </b></label>
		<span class="required">*</span>
		<div style="clear:both;"></div>
		'.cust_catcha_html().'
		<div style="clear:both;"></div>
		<label for="OSOLmulticaptcha_keystring">'.__('Type the text displayed above', 'wpcaptchadomain').':</label>
		<input id="OSOLmulticaptcha_keystring" name="OSOLmulticaptcha_keystring" size="15" type="text" />
		<div style="clear:both;"></div>
		</p>';
		
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
		wp_die( __('CAPTCHA cannot be empty.', 'wpcaptchadomain' ) );

	// captcha was matched
	if($_SESSION['OSOLmulticaptcha_keystring'] == $_REQUEST['OSOLmulticaptcha_keystring']) return($comment);
	elseif(get_option('cust_captcha_status') == 'enabled' && (!isset($_REQUEST['OSOLmulticaptcha_keystring']) || $_SESSION['OSOLmulticaptcha_keystring'] != $_REQUEST['OSOLmulticaptcha_keystring']))
	{
		wp_die( __('Error: Incorrect CAPTCHA. Press your browser\'s back button and try again.', 'wpcaptchadomain'));
	}
} 

/* <!-- Captcha for Comments authentication ends here --> */



// Add captcha in the register form
$register_captcha = get_option('wpcaptcha_register');
//if($register_captcha == 'yes'){
if(get_option('cust_captcha_status') ==  'enabled')
{

	add_action('register_form', 'include_cust_captcha_register');
	add_action( 'register_post', 'include_cust_captcha_register_post', 10, 3 );
	add_action( 'signup_extra_fields', 'include_cust_captcha_register' );
	add_filter( 'wpmu_validate_user_signup', 'include_cust_captcha_register_validate' );
}

/* Function to include captcha for register form */
function include_cust_captcha_register($default){
	echo '<p class="register-form-captcha">	
			<label for="captcha"><b>'. __('Captcha', 'wpcaptchadomain').' </b></label>
			<span class="required">*</span>
			<div style="clear:both;"></div>
			'.cust_catcha_html().'
			<div style="clear:both;"></div>
			<label for="OSOLmulticaptcha_keystring">'.__('Type the text displayed above', 'wpcaptchadomain').':</label>
			<input id="OSOLmulticaptcha_keystring" name="OSOLmulticaptcha_keystring" size="15" type="text" />
			</p>';
	return true;
}

/* This function checks captcha posted with registration */
function include_cust_captcha_register_post($login,$email,$errors) {

	// If captcha is blank - add error
	if ( isset( $_REQUEST['OSOLmulticaptcha_keystring'] ) && "" ==  $_REQUEST['OSOLmulticaptcha_keystring'] ) {
		$errors->add('captcha_blank', '<strong>'.__('ERROR', 'wpcaptchadomain').'</strong>: '.__('Please complete the CAPTCHA.', 'wpcaptchadomain'));
		return $errors;
	}

	/*if ( isset( $_REQUEST['OSOLmulticaptcha_keystring'] ) && ($_SESSION['OSOLmulticaptcha_keystring'] == $_REQUEST['OSOLmulticaptcha_keystring'] )) {
					// captcha was matched						
	} else */
	if(get_option('cust_captcha_status') == 'enabled' && (!isset($_REQUEST['OSOLmulticaptcha_keystring']) || $_SESSION['OSOLmulticaptcha_keystring'] != $_REQUEST['OSOLmulticaptcha_keystring']))
	{
		$errors->add('captcha_wrong', '<strong>'.__('ERROR', 'wpcaptchadomain').'</strong>: '.__('That CAPTCHA was incorrect.', 'wpcaptchadomain'));
	}
  return($errors);
} 
/* End of the function include_cust_captcha_register_post */

function include_cust_captcha_register_validate($results) {
	if ( isset( $_REQUEST['OSOLmulticaptcha_keystring'] ) && "" ==  $_REQUEST['OSOLmulticaptcha_keystring'] ) {
		$results['errors']->add('captcha_blank', '<strong>'.__('ERROR', 'wpcaptchadomain').'</strong>: '.__('Please complete the CAPTCHA.', 'wpcaptchadomain'));
		return $results;
	}

	/*if ( isset( $_REQUEST['OSOLmulticaptcha_keystring'] ) && ($_SESSION['OSOLmulticaptcha_keystring'] == $_REQUEST['OSOLmulticaptcha_keystring'] )) {
					// captcha was matched						
	} else*/ 
	if(get_option('cust_captcha_status') == 'enabled' && (!isset($_REQUEST['OSOLmulticaptcha_keystring']) || $_SESSION['OSOLmulticaptcha_keystring'] != $_REQUEST['OSOLmulticaptcha_keystring']))
	{
		$results['errors']->add('captcha_wrong', '<strong>'.__('ERROR', 'wpcaptchadomain').'</strong>: '.__('That CAPTCHA was incorrect.', 'wpcaptchadomain'));
	}
  return($results);
}
/* End of the function include_cust_captcha_register_validate */


$lost_captcha = get_option('wpcaptcha_lost');
// Add captcha into lost password form
//if($lost_captcha == 'yes'){
if(get_option('cust_captcha_status') ==  'enabled')
{
	add_action( 'lostpassword_form', 'include_cust_captcha_lostpassword' );
	add_action( 'lostpassword_post', 'include_cust_captcha_lostpassword_post', 10, 3 );
}

/* Function to include captcha for lost password form */
function include_cust_captcha_lostpassword($default){
	echo '<p class="lost-form-captcha">
		<label for="captcha"><b>'. __('Captcha', 'wpcaptchadomain').' </b></label>
		<span class="required">*</span>
		<div style="clear:both;"></div>
		'.cust_catcha_html().'
		<div style="clear:both;"></div>
		<label for="OSOLmulticaptcha_keystring">'.__('Type the text displayed above', 'wpcaptchadomain').':</label>
		<input id="OSOLmulticaptcha_keystring" name="OSOLmulticaptcha_keystring" size="15" type="text" />
		</p>';	
}

function include_cust_captcha_lostpassword_post() {
	if( isset( $_REQUEST['user_login'] ) && "" == $_REQUEST['user_login'] )
		return;

	// If captcha doesn't entered
	if ( isset( $_REQUEST['OSOLmulticaptcha_keystring'] ) && "" ==  $_REQUEST['OSOLmulticaptcha_keystring'] ) {
		wp_die( __( 'Please complete the CAPTCHA.', 'wpcaptchadomain' ) );
	}
	
	// Check entered captcha
	/*if ( isset( $_REQUEST['OSOLmulticaptcha_keystring'] ) && ($_SESSION['OSOLmulticaptcha_keystring'] == $_REQUEST['OSOLmulticaptcha_keystring'] )) {
		return;
	} else {*/
	if(get_option('cust_captcha_status') == 'enabled' && (!isset($_REQUEST['OSOLmulticaptcha_keystring']) || $_SESSION['OSOLmulticaptcha_keystring'] != $_REQUEST['OSOLmulticaptcha_keystring']))
	{
		wp_die( __( 'Error: Incorrect CAPTCHA. Press your browser\'s back button and try again.', 'wpcaptchadomain' ) );
	}
}



//***********************************************CONTACT US PART
// [cccontact toEmail="abc@bcd.com" linkToModal="yes" cccontact_unique_id="1"]

add_shortcode( 'cccontact', 'cust_captcha_contact_func' );
function cust_captcha_contact_func( $atts ) {
	extract( shortcode_atts( array(
		'toemail' => get_option('cust_captcha_contact_email'),
		'linktomodal' => 'no',
		'cccontact_unique_id' => '-1'
	), $atts ) );
	if(!session_id()){
		session_start();
	}
	$postid = get_the_ID();
	$toMailSessionVar = 'toemail-'.$postid."_".$cccontact_unique_id;
	$_SESSION[$toMailSessionVar] = $toemail;
	ob_start();
	include(CUST_CAPTCHA_FOLDER.'/cccontact_form.php');
    $output_string=ob_get_contents();
    ob_end_clean();
	return $output_string;//"cccontact :".$toemail." , ".$linktomodal." ,$toMailSessionVar";
}
function cccontact_validate_notify_form()
{
	$validation_result = '';
	$input_fields = array('subject','message','name','email');
	foreach($input_fields as $field_name)
	{
		if(!isset($_REQUEST["cccontact_".$field_name]) || (trim($_REQUEST["cccontact_".$field_name]) == ''))
		{
			$validation_result .= $field_name ." should not be blank <br />";
		}
		
	}
		$emailFilter = "/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/";
		if(!preg_match($emailFilter,$_REQUEST['cccontact_email']))
		{
			$validation_result .= "Please enter a valid from email <br />";
		}
		
		$phoneFilter = "/^([0-9_\.\-\s])+$/";
		/*if(!preg_match($phoneFilter,$_REQUEST['phone']))
		{
			$validation_result .= "Please enter a valid phone number <br />";
		}*/
	return $validation_result;
	
}
$validation_result = '';
if(isset($_REQUEST['cccontact_action']) && $_REQUEST['cccontact_action'] == 'cust_captcha_contact_submit')
{
	$validation_result = cccontact_validate_notify_form();
	if($validation_result  != '')
	{
		die($validation_result);
	}
																							
	if ( isset( $_REQUEST['OSOLmulticaptcha_keystring'] ) && "" ==  $_REQUEST['OSOLmulticaptcha_keystring'] ) {
		wp_die( __( 'Please complete the CAPTCHA.', 'wpcaptchadomain' ) );
	}
	if(!session_id()){
		session_start();
	}
	// Check entered captcha
	/*if ( isset( $_REQUEST['OSOLmulticaptcha_keystring'] ) && ($_SESSION['OSOLmulticaptcha_keystring'] == $_REQUEST['OSOLmulticaptcha_keystring'] )) {
		return;
	} else {*/
	if(get_option('cust_captcha_status') == 'enabled' && (!isset($_REQUEST['OSOLmulticaptcha_keystring']) || $_SESSION['OSOLmulticaptcha_keystring'] != $_REQUEST['OSOLmulticaptcha_keystring']))
	{
		
		wp_die( __( 'Error: Incorrect CAPTCHA. Press your browser\'s back button and try again.', 'wpcaptchadomain' ) );
	}
	
	
	
	$toMailSessionVar = $_REQUEST['cccontact_toMailSessionVar'];
	$toemail = isset($_SESSION[$toMailSessionVar])?$_SESSION[$toMailSessionVar]:get_option('cust_captcha_contact_email');
	//die($toMailSessionVar." : " .$_SESSION[$toMailSessionVar]. " : " .$toemail);
		$summary =	"<br />\r\n Message:<br />\r\n".nl2br($_REQUEST['cccontact_message']);
	
		$subject = $_REQUEST['cccontact_subject'];
		
		$boundary = uniqid('np');
		
		 //$message = "This is a MIME encoded message."; 
		 $message = 'This message was sent via PHP !' ;
				   
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
		if (@mail($toemail, $subject, $message, $headers)) {
		  wp_die( __('mail sent Successfully!' . "\n"));//.'to '.$to
		}
		else {
		  wp_die( __('mail() Failure!.contact site admin' . "\n"));
		}
		
}




//***********************ADMIN SECTION**************************
/* admin pages section */
function cust_captcha_settings()
{
	require_once(CUST_CAPTCHA_FOLDER."/captcha-customization-options.php");
}
function cust_captcha_contact_settings()
{
	require_once(CUST_CAPTCHA_FOLDER."/contact-customization-options.php");
}
if(isset($_REQUEST['show_cust_captcha']) && $_REQUEST['show_cust_captcha'] == 'true')
{
	require_once(CUST_CAPTCHA_FOLDER."/displayCaptcha.php");
}
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'cust_captcha_contact_email_submit')
{
	if (is_admin()) require_once(ABSPATH . 'wp-includes/pluggable.php');
	if(!is_super_admin()){die('should be logged in as admin to update contact email');}
	update_option('cust_captcha_contact_email',$_REQUEST['cust_captcha_contact_email']);
}
/* admin pages section ends here */
?>