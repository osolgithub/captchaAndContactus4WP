<?php
/*
Plugin Name: Captcha and Contact Us Forms for Wordpress
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
// the above block is for wordpress to detect and assimilate this plugin as part of its system
// below block is for doxygen
/**
@mainpage Captcha and Contact Us Forms for Wordpress
Adds OSOLMulticaptcha (http://www.outsource-online.net/osolmulticaptcha-simplest-php-captcha-for-html-forms.html ) to wordpress forms for registration,login,forgot password and comment.

Additionally ,this will enable admin to add contact us form with the same captcha in any wordpress page
just need to insert the code [cccontact] in a page where you want to show contact us form

@par Requirements
PHP GD Library must be available in the server
safe mode must be turned off

The above requirements are default settings in most PHP hosts.however if the captcha isnt showing up you need to check those settings

	
@date 21st October 2022
@copyright {This project is released under the GNU Public License.}
@author Sreekanth Dayanand
@note short code for this plugin will be 'osolwpccc'. To be used in 'load_plugin_textdomain' and text translation functions. This line is not mandatory but useful for future reference for developers , while modifying the plugin
*/
/**
* @file custCaptchaContact.php
* @brief Bootstrap file for this plugin. 
* @details Starting point of the project.\n
* This file bootstraps the operations of this project\n
* This documentation is shown because *file* tag is used.\n
* This will appear under  Main Project &gt;&gt; Files &gt;&gt; File List &gt;&gt; thisFileName \n
\par Hooks Used:
hooks are extracted with
//add_action\('([^']+)',\s*'([^']+)'  replace with add_action('\1',[$OSOL_CCC_HandlerFrontEnd_inst,'\2']
//add_action\('([^']+)',([^/\r\n]+)
**add_action hooks**
	1. add_action('wp', function () {	// see all functions hooked to 'wp_footer'			
	2. add_action('init',// init for admin is 'admin_init' hook				
	3. add_action'wp_head',				
	4. add_action'wp_enqueue_scripts',//load jquery in wp_head for contact page since jQuery(document).ready is used				
	5. add_action( 'wp_footer', [$OSOLCCC_CommonClass_inst,'add_ccc_onload'] ); // For front-end to call refresh captcha				
	6. add_action'login_form',				
	7. add_action( 'comment_form_after_fields', [$OSOLCCC_Frontend_inst,'include_cust_captcha_comment_form_wp3'], 1 );				
	8. add_action( 'comment_form_logged_in_after', [$OSOLCCC_Frontend_inst,'include_cust_captcha_comment_form_wp3'], 1 );				
	9. add_action( 'comment_form', [$OSOLCCC_Frontend_inst,'include_cust_captcha_comment_form'] );				
	10. add_action'register_form',// add captcha html in register form				
	11. add_action( 'register_post', [$OSOLCCC_Frontend_inst,'include_cust_captcha_register_post'], 10, 3 );// perform plugin specific actions upon user registration				
	12. add_action( 'signup_extra_fields', [$OSOLCCC_Frontend_inst,'include_cust_captcha_register'] );// add captcha html in register form				
	13. add_action( 'lostpassword_form', [$OSOLCCC_Frontend_inst,'include_cust_captcha_lostpassword'] );				
	14. add_action( 'lostpassword_post', [$OSOLCCC_Frontend_inst,'include_cust_captcha_lostpassword_post'], 10, 3 );				
	15. add_action'admin_init',				
	16. add_action'admin_menu',				
	17. add_action( 'admin_footer',  [$OSOLCCC_CommonClass_inst,'add_ccc_onload'] ); // For back-end  to call refresh captcha				
	18. add_action'admin_enqueue_scripts',//load jquery in wp_head for contact page since jQuery(document).ready is used				
	19. add_action'wp_ajax_cccontact_display_captcha',// executed when logged in				
	20. add_action'wp_ajax_nopriv_cccontact_display_captcha',// executed when logged out				
	21. add_action('wp_ajax_cccontact_tb_show_modal' , [$OSOLCCC_Frontend_inst,'cccontact_tb_show_modal']);// executed when logged in				
	22. add_action'wp_ajax_nopriv_cccontact_tb_show_modal',// executed when logged out				
	23. add_action'wp_ajax_cccontact_validate_ajax',// executed when logged in				
	24. add_action'wp_ajax_nopriv_cccontact_validate_ajax',// executed when logged out	
**add_filter hooks **
	1. add_filter('login_errors',[$OSOLCCC_Frontend_inst,'cust_captcha_login_errors']);
	2. add_filter( 'login_redirect', [$OSOLCCC_Frontend_inst,'include_cust_captcha_login_redirect'], 10, 3 );	
	3. add_filter( 'preprocess_comment', [$OSOLCCC_Frontend_inst,'include_cust_captcha_comment_post'] );
	4. add_filter( 'wpmu_validate_user_signup', [$OSOLCCC_Frontend_inst,'include_cust_captcha_register_validate'] );// perform validation of captcha
* @warning without *file* tag, non class files are not documented\n
* Also no global variables will be documented
*
*/
/**
 \defgroup jsfiles Vanilla JS
 This group is for holding non class JS files
*/

//replace all wpcaptchadomain
/*! 
 *  \brief constant holding file path of this plugin.
 * @details this constant is defined for ease of usage in all classes.\n
 This is used in autoloader and for loading template files
 */
define('CUST_CAPTCHA_FOLDER',dirname(__FILE__));
/*! 
 *  \brief constant holding URL path of this plugin.
 * @details this constant is defined for ease of usage in all classes.\n
 This is used in autoloader and for loading public assets of this plugin.
 */
define('CUST_CAPTCHA_DIR_URL', plugin_dir_url(__FILE__));
// auto load Helpers/Frontend.php while calling new \OSOLCCC\Helpers\Frontend()
/*! \fn osolAutoLoadRegisterCalled() 
 *  \brief Dummy function to mention **spl_autoload_register(function ($class)** is called.
 * After registering this autoload function with SPL, the following line
 * would cause the function to attempt to load the \Foo\Bar\Baz\Qux class
 * from /path/to/project/src/Baz/Qux.php:
 *
 *      new \Foo\Bar\Baz\Qux;
 *
 *  \param function Function that maps called classes to appropriate source files.
 *  \exception std::fileNotFound No such file check the spelling of {$class}.
 *  \return void.
 */
 function osolAutoLoadRegisterCalled(){} 
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

//require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'OSOLmulticaptcha.php');
$OSOLCCC_Frontend_inst = \OSOLCCC\Hooks\Frontend::getInstance();
$OSOLCCC_Admin_inst = \OSOLCCC\Hooks\Admin::getInstance();
$OSOLCCC_CommonClass_inst = \OSOLCCC\Hooks\Common::getInstance();


/**
*  @brief Determines wether captcha is enabled, ie to be shown in forms
*  @details
    It is set in admin panel, in "Captcha Settings"
*/
$GLOBALS['OSOLMulticaptcha_captcha_enabled'] = get_option('cust_captcha_status');

/**
*  @brief Determines wether captcha should be based on session/cookie(default) or GDPR Compliant
*  @details
    It is set in admin panel, in "Captcha Settings"
*/
$GLOBALS['OSOLMulticaptcha_gdprCompliantNoCookie'] = get_option('OSOLMulticaptcha_gdprCompliantNoCookie');


/**
*  @brief Determines wether captcha is enabled in login form
*  @details
    It is set in admin panel, in "Captcha Settings"
*/
$login_captcha = get_option('OSOLMulticaptcha_cust_captcha_login');
/**
*  @brief Determines wether captcha is enabled in comment form
*  @details
    It is set in admin panel, in "Captcha Settings"
*/
$comment_captcha = get_option('OSOLMulticaptcha_cust_captcha_comments');
/**
*  @brief Determines wether captcha is enabled in sign up form
*  @details
    It is set in admin panel, in "Captcha Settings"
*/
$register_captcha = get_option('OSOLMulticaptcha_cust_captcha_register');
/**
*  @brief Determines wether captcha is enabled in loas password form
*  @details
    It is set in admin panel, in "Captcha Settings"
*/
$lost_captcha = get_option('OSOLMulticaptcha_cust_captcha_lost');
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

if($GLOBALS['OSOLMulticaptcha_captcha_enabled'] ==  'enabled' && ($login_captcha == 'yes'))
{
	add_action('login_form',[$OSOLCCC_Frontend_inst,'include_cust_captcha_login'] );
	add_filter('login_errors',[$OSOLCCC_Frontend_inst,'cust_captcha_login_errors']);
	add_filter( 'login_redirect', [$OSOLCCC_Frontend_inst,'include_cust_captcha_login_redirect'], 10, 3 );	
}
/* Captcha for login authentication ends here */ 





/* Captcha for Comments starts here */
//if($comment_captcha == 'yes'){
if($GLOBALS['OSOLMulticaptcha_captcha_enabled'] ==  'enabled' && ($comment_captcha == 'yes'))
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
if($GLOBALS['OSOLMulticaptcha_captcha_enabled'] ==  'enabled' && ($register_captcha == 'yes'))
{

	add_action('register_form',[$OSOLCCC_Frontend_inst,'include_cust_captcha_register'] );// add captcha html in register form
	add_action( 'register_post', [$OSOLCCC_Frontend_inst,'include_cust_captcha_register_post'], 10, 3 );// perform plugin specific actions upon user registration
	
	add_action( 'signup_extra_fields', [$OSOLCCC_Frontend_inst,'include_cust_captcha_register'] );// add captcha html in register form
	add_filter( 'wpmu_validate_user_signup', [$OSOLCCC_Frontend_inst,'include_cust_captcha_register_validate'] );// perform validation of captcha
}






// Add captcha into lost password form
//if($lost_captcha == 'yes'){
if($GLOBALS['OSOLMulticaptcha_captcha_enabled'] ==  'enabled' && ($lost_captcha == 'yes'))
{
	add_action( 'lostpassword_form', [$OSOLCCC_Frontend_inst,'include_cust_captcha_lostpassword'] );
	add_action( 'lostpassword_post', [$OSOLCCC_Frontend_inst,'include_cust_captcha_lostpassword_post'], 10, 3 );
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
// depending on wether logged in or not, the following hooks gets triggered when calling url contains action=cccontact_display_captcha
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