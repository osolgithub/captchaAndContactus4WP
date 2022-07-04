<?php
/*
Plugin Name: Customizable Captcha and contact us
Plugin URI: http://www.outsource-online.net/
Description: Plugin to add captcha to core wordpress forms and additional option for contact us page.Just need to insert the code [cccontact] in a page where you want to show contact us form. 
Version:  1.0.2
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
/*
Adds OSOLMulticaptcha (http://www.outsource-online.net/osolmulticaptcha-simplest-php-captcha-for-html-forms.html ) to wordpress forms for registration,login,forgot password and comment.

Additionally ,this will enable admin to add contact us form with the same captcha in any wordpress page
just need to insert the code [cccontact] in a page where you want to show contact us form

Requirements
PHP GD Library must be available in the server
safe mode must be turned off

The above requirements are default settings in most PHP hosts.however if the captcha isnt showing up you need to check those settings

*/
// short code for this plugin will be 'osolwpccc'. To be used in 'load_plugin_textdomain' and text translation functions. This line is not mandatory but useful for future reference for developers , while modifying the plugin
//replace all wpcaptchadomain
define('CUST_CAPTCHA_FOLDER',dirname(__FILE__));
define('CUST_CAPTCHA_DIR_URL', plugin_dir_url(__FILE__));
// auto load Helpers/Frontend.php while calling new \OSOLCCC\Helpers\Frontend() 
if(!function_exists('version_compare') || version_compare(phpversion(), '5.1.0', '<'))die("Minimum version required for 'Customizable Captcha and contact us' plugin is 5.1.0");
spl_autoload_register(function ($class) {
	// project-specific namespace prefix
	$prefix = 'OSOLCCC\\';	
	// base directory for the namespace prefix
	$base_dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'MVC/';
	//die($class . " " . str_replace($prefix,'',$class));
	// does the class use the namespace prefix?
	$len = strlen($prefix);
	if (strncmp($prefix, $class, $len) !== 0) {
		// no, move to the next registered autoloader
		return;
	}	
	// get the relative class name
	$relative_class = substr($class, $len);
	// replace the namespace prefix with the base directory, replace namespace
	// separators with directory separators in the relative class name, append
	// with .php
	$mappedFile = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
	try {
		if (file_exists($mappedFile)) {
			require $mappedFile;
		}
		else
		{
			//die('<p style="background:#f00">ERROR!!!!! file : '.$mappedFile. " does not exist to autoload for ".$class ."</p>");
			//throw new CustomException('ERROR!!!!! file : '.$mappedFile. " does not exist to autoload for ".$class );
			throw new Exception('ERROR!!!!! file : '.$mappedFile. " does not exist to autoload for ".$class );
		}
	}
	catch (Exception $e) {
	  //display custom message
	  $debug_trace = debug_backtrace();
	  $fileAndLineno = "file : {$debug_trace[1]['file']} , Line #: {$debug_trace[1]['line']}";
	  echo $e->getMessage() . $fileAndLineno;
	}
});

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'OSOLmulticaptcha.php');
$OSOLCCC_Frontend_inst = \OSOLCCC\Hooks\Frontend::getInstance();
$OSOLCCC_Admin_inst = \OSOLCCC\Hooks\Admin::getInstance();
$OSOLCCC_CommonClass_inst = \OSOLCCC\Hooks\Common::getInstance();

$GLOBALS['OSOLMulticaptcha_gdprCompliantNoCookie'] = get_option('OSOLMulticaptcha_gdprCompliantNoCookie');



/* Hook to store the plugin status, triggered when plugin is activated and deactivated */
register_activation_hook(__FILE__, [$OSOLCCC_Admin_inst,'on_cust_captcha_enabled']);
register_deactivation_hook(__FILE__, [$OSOLCCC_Admin_inst,'on_cust_captcha_disabled']);

/*Hook to internationalize*/
load_plugin_textdomain('osolwpccc', false, dirname( plugin_basename(__FILE__)).'/languages');

/* 
// see all functions hooked to 'wp_footer'
add_action('wp', function () {
    echo '<pre>';
    print_r($GLOBALS['wp_filter']['wp_footer']);
    echo '</pre>';
    exit;
}); */
//add_action\('([^']+)',\s*'([^']+)'  replace with add_action('\1',[$OSOL_CCC_HandlerFrontEnd_inst,'\2']

/* Hook to initialize sessions */
add_action('init', [$OSOLCCC_Frontend_inst,'init4Frontend']);// init for admin is 'admin_init' hook
function osolwpccc_custom_javascript() {
    ?>
        <script>
          // your javscript code goes here
		  console.log('osolwpccc_custom_javascript')
        </script>
    <?php
}
add_action('wp_head', 'osolwpccc_custom_javascript');
add_action("wp_enqueue_scripts", [$OSOLCCC_CommonClass_inst,"cccontact_jquery_enqueue"], 11);//load jquery in wp_head for contact page since jQuery(document).ready is used 
add_action( 'wp_footer', [$OSOLCCC_CommonClass_inst,'add_ccc_onload'] ); // For front-end to call refresh captcha


/* Captcha for login authentication starts here */ 

$login_captcha = get_option('OSOLMulticaptcha_cust_captcha_login');
//if($login_captcha == 'yes')
if(get_option('cust_captcha_status') ==  'enabled' && ($login_captcha == 'yes'))
{
	add_action('login_form',[$OSOLCCC_Frontend_inst,'include_cust_captcha_login'] );
	add_filter('login_errors',[$OSOLCCC_Frontend_inst,'cust_captcha_login_errors']);
	add_filter( 'login_redirect', [$OSOLCCC_Frontend_inst,'include_cust_captcha_login_redirect'], 10, 3 );	
}
/* Captcha for login authentication ends here */ 





/* Captcha for Comments starts here */
$comment_captcha = get_option('OSOLMulticaptcha_cust_captcha_comments');
//if($comment_captcha == 'yes'){
if(get_option('cust_captcha_status') ==  'enabled' && ($comment_captcha == 'yes'))
{
	global $wp_version;
	if( version_compare($wp_version,'3','>=') ) { // wp 3.0 +
		add_action( 'comment_form_after_fields', [$OSOLCCC_Frontend_inst,'include_cust_captcha_comment_form_wp3'], 1 );
		add_action( 'comment_form_logged_in_after', [$OSOLCCC_Frontend_inst,'include_cust_captcha_comment_form_wp3'], 1 );
	}	
	// for WP before WP 3.0
	add_action( 'comment_form', [$OSOLCCC_Frontend_inst,'include_cust_captcha_comment_form'] );	
	add_filter( 'preprocess_comment', [$OSOLCCC_Frontend_inst,'include_cust_captcha_comment_post'] );
}
/* Captcha for Comments ends here */




/* <!-- Captcha for Comments authentication ends here --> */



// Add captcha in the register form
$register_captcha = get_option('OSOLMulticaptcha_cust_captcha_register');
//if($register_captcha == 'yes'){
if(get_option('cust_captcha_status') ==  'enabled' && ($register_captcha == 'yes'))
{

	add_action('register_form',[$OSOLCCC_Frontend_inst,'include_cust_captcha_register'] );// add captcha html in register form
	add_action( 'register_post', [$OSOLCCC_Frontend_inst,'include_cust_captcha_register_post'], 10, 3 );// perform plugin specific actions upon user registration
	
	add_action( 'signup_extra_fields', [$OSOLCCC_Frontend_inst,'include_cust_captcha_register'] );// add captcha html in register form
	add_filter( 'wpmu_validate_user_signup', [$OSOLCCC_Frontend_inst,'include_cust_captcha_register_validate'] );// perform validation of captcha
}





$lost_captcha = get_option('OSOLMulticaptcha_cust_captcha_lost');
// Add captcha into lost password form
//if($lost_captcha == 'yes'){
if(get_option('cust_captcha_status') ==  'enabled' && ($lost_captcha == 'yes'))
{
	add_action( 'lostpassword_form', 'include_cust_captcha_lostpassword' );
	add_action( 'lostpassword_post', 'include_cust_captcha_lostpassword_post', 10, 3 );
}


/* frontend  hooks and filters ends here */
 


//***********************ADMIN SECTION**************************
/* admin pages section */

// hook for initing admin, mainly processing forms, loading thickbox etc
add_action('admin_init', [$OSOLCCC_Admin_inst,'init4Admin']);
/* Hook to initalize the admin menu */
add_action('admin_menu', [$OSOLCCC_Admin_inst,'show_cust_captcha_contact_plugin_menu']);

add_action( 'admin_footer',  [$OSOLCCC_CommonClass_inst,'add_ccc_onload'] ); // For back-end  to call refresh captcha			
add_action("admin_enqueue_scripts", [$OSOLCCC_CommonClass_inst,"cccontact_jquery_enqueue"], 11);//load jquery in wp_head for contact page since jQuery(document).ready is used 
/* admin pages section ends here */



//******************************* CAPTCHA AND CONTACT US FORM HOOKS


//***********************************************display captcha hooks 
//https://developer.wordpress.org/reference/hooks/wp_ajax_action/ 
// depending on wether logged in or not, the following hooks gets triggered when calling url contains action=cccontact_cccontact_display_captcha
add_action('wp_ajax_cccontact_display_captcha', [$OSOLCCC_CommonClass_inst,'cust_captcha_display_captcha']);// executed when logged in
add_action('wp_ajax_nopriv_cccontact_display_captcha', [$OSOLCCC_CommonClass_inst,'cust_captcha_display_captcha']);// executed when logged out

//***********************************************CONTACT US PART
// [cccontact toEmail="abc@bcd.com" linkToModal="yes" cccontact_unique_id="1"]

add_shortcode( 'cccontact', [$OSOLCCC_Frontend_inst,'cust_captcha_contact_func'] );

//echo "HI Captcha";
//https://developer.wordpress.org/reference/hooks/wp_ajax_action/ 
// the following hook gets triggered when calling url contains action=cccontact_tb_show_modal
add_action('wp_ajax_cccontact_tb_show_modal' , [$OSOLCCC_Frontend_inst,'cccontact_tb_show_modal']);// executed when logged in
add_action('wp_ajax_nopriv_cccontact_tb_show_modal', [$OSOLCCC_Frontend_inst,'cccontact_tb_show_modal'] ); // executed when logged out

// the following hook gets triggered when calling url contains action=cccontact_validate_ajax
add_action('wp_ajax_cccontact_validate_ajax', [$OSOLCCC_Frontend_inst,'cust_captcha_contact_validate_contact_ajax']);// executed when logged in
add_action('wp_ajax_nopriv_cccontact_validate_ajax', [$OSOLCCC_Frontend_inst,'cust_captcha_contact_validate_contact_ajax']);// executed when logged out
?>