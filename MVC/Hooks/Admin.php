<?php
/**
* @file FrontEnd.php
* \OSOLCCC\Helpers\Frontend class: Used for OOPfying the plugin
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

/**
*  @brief OSOL_CCC_Handler class: Used for OOPfying the pluiugin
*
*
*  @author Sreekanth
*  @date 23rd June 2022
*  @details  
 This class encapsulates all custom methods for for backend of the plugin. \n
 This class also  encapsulates all backend hooks for the plugin. \n
 - This class deals with
	1. Adding Plugin Configuration link in admin side
	3. display configuration options of captcha
	3. display "contact us" form configuration options
	4. Save/Update configuration forms submissions
	

 */
namespace OSOLCCC\Hooks;
Class Admin extends \OSOLCCC\SingletonParent{
	
	
	
	//wordpress hooks methods for backend starts here
	
	function on_cust_captcha_enabled(){
		update_option('cust_captcha_status', 'enabled');
		if(get_option('cust_captcha_contact_email') == '')
		{
			update_option('cust_captcha_contact_email',get_option('admin_email'));
		}
	}
	function on_cust_captcha_disabled(){
		update_option('cust_captcha_status', 'disabled');
	}
	function init4Admin(){
		$this->processAdminFormSubmissions();
		add_thickbox(); // thickbox wont load in admin via wp_enque_scripts
	}//function init4Admin(){
	function processAdminFormSubmissions(){
		if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'cust_captcha_contact_email_submit')
		{
			if (is_admin()) require_once(ABSPATH . 'wp-includes/pluggable.php');//`wp_get_current_user` is called in is_super_admin(), for that `pluggable.php` must be explicitly loaded
			if(!is_super_admin()){die('should be logged in as admin to update contact email');}
			update_option('cust_captcha_contact_email',$_REQUEST['cust_captcha_contact_email']);
			//die("email updated");
		}
		if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'cust_captcha_options_submit')
		{
			if (is_admin()) require_once(ABSPATH . 'wp-includes/pluggable.php');//`wp_get_current_user` is called in is_super_admin(), for that `pluggable.php` must be explicitly loaded
			if(!is_super_admin()){die('should be logged in as admin to update captcha settings');}
			foreach($_REQUEST as $requestVar => $requestVarVal)
			{
				if(preg_match("@OSOLMulticaptcha_(.+)@",$requestVar))
				{
					update_option($requestVar,$requestVarVal);
				}
			}
			if(!isset($_REQUEST['OSOLMulticaptcha_caseInsensitive']))
			{
				update_option('OSOLMulticaptcha_caseInsensitive',0);
			}
			if(!isset($_REQUEST['OSOLMulticaptcha_gdprCompliantNoCookie']))
			{
				update_option('OSOLMulticaptcha_gdprCompliantNoCookie',0);
			}
			
			update_option('cust_captcha_contact_email',$_REQUEST['cust_captcha_contact_email']);
		}
	}//function processAdminFormSubmissions()
	function show_cust_captcha_contact_plugin_menu() {
		add_options_page('Customiazable Captcha and contact us plugin:Captcha settings', 'Captcha Settings', 'manage_options', 'cust-captcha-settings', [$this,'show_cust_captcha_settings']);
		
		add_options_page('Customiazable Captcha and contact us plugin:Contact page settings', 'Contact page settings', 'manage_options', 'cust-contact-settings', [$this,'show_cust_captcha_contact_settings']);
		

		
	}//function cust_captcha_contact_plugin_menu() {
	function show_cust_captcha_settings()
	{
		require_once(CUST_CAPTCHA_FOLDER."/captcha-customization-options.php");
		
	}
	
	function show_cust_captcha_contact_settings()
	{
		require_once(CUST_CAPTCHA_FOLDER."/contact-customization-options.php");
		/* ?>
		<a href="javascript:<?php echo \OSOLCCC\Helpers\Frontend::getInstance()->cccontact_tb_show_call("hello","test Subject");?>">Show Contact us In Modal</a>
		<?php */
	}
	
}//Class Backend{
?>