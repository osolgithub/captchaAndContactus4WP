<?php
/**
* \class OSOLCCC::Hooks::Common 
*  \brief OSOL_CCC_Handler class: Used for OOPfying the wordpress plugin
*  \details  
 This class encapsulates all custom methods for for backend of the plugin. \n
 This class also  encapsulates all backend hooks for the plugin. \n
 - This class deals with
	1. Adding Plugin Configuration link in admin side
	3. display configuration options of captcha
	3. display "contact us" form configuration options
	4. Save/Update configuration forms submissions
 
instantiation 
\OSOLCCC\Hooks\Common::getInstance()
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
class Common extends \OSOLCCC\SingletonParent{
	
	
	//declare variables pertinant to the functionality
	
	
	protected function __construct(){
		//echo "initialized \OSOLCCCF\Helpers\Backend()!!!";
	}	
	
	/* <!-- wordpress hooks starts here --> */
	/**
     *  @brief add_ccc_onload : called in \OSOLCCC\Hooks\Frontend->include_cust_captcha_login() and also in add_action( 'admin_footer', & add_action( 'wp_footer',
     *
     *  @param [in] no input paramaeters
     *  @return Returns void
     *  @author Sreekanth Dayanand
     *  @date 23rd June 2022
     *  @details 
		calls js function refreshOSOLMultiCaptchaImage() on window.onload
	    called in  
		1. add_action( 'admin_footer', & 
		2. add_action( 'wp_footer', 
	    and also in 
	    \OSOLCCC\Hooks\Frontend->include_cust_captcha_login() //in login pages, hooks ie "add_action( 'admin_footer', & add_action( 'wp_footer'," don't work since there is no  <footer>
		
     */
	function add_ccc_onload() {
		echo '<p>' . __( 'This will be inserted at the bottom of admin page', 'textdomain' ) . '</p>';
		$this->cccontact_jquery_enqueue();
		$this->add_ajaxurl_cdata_to_front();
		if($GLOBALS['OSOLMulticaptcha_gdprCompliantNoCookie'])
		{
		?>
		<script type="text/javascript">
		osolwpccc_onload_callback = function() { console.log("calling refreshOSOLMultiCaptchaImage()");refreshOSOLMultiCaptchaImage(); }; // test function

		if( typeof jQuery == "function" ) { 
			jQuery(osolwpccc_onload_callback); // abbreviateion of jQuery(document).ready(function(){...}), ie document.ready
		} else {
			document.getElementsByTagName('body')[0].onload = osolwpccc_onload_callback; // body.onload
			
		}
		</script>
		<?php
		}//if($GLOBALS['OSOLMulticaptcha_gdprCompliantNoCookie'])
	}//function add_ccc_onload()
	function cccontact_jquery_enqueue() {

	   wp_enqueue_script('jquery');
	   //also initiate thick box
		if(!is_admin()) // thickbox wont load in admin
		{
			//wp_enqueue_script('jquery');
			wp_enqueue_script('thickbox',null,array('jquery'));
			wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
		}
	}
	/* <!-- Display captcha hooks starts here --> */
	function cust_captcha_display_captcha()
	 {
		//if(isset($_REQUEST['show_cust_captcha']) && $_REQUEST['show_cust_captcha'] == 'true')
		{
			//require_once(CUST_CAPTCHA_FOLDER."/displayCaptcha.php");
			\OSOLCCC\Views\ContactusView::getInstance()->displayCaptcha();
		}
	 }
	 /**
     *  @brief add_ajaxurl_cdata_to_front : called in \OSOLCCC\Hooks\Common->add_ccc_onload() 
     *
     *  @param [in] no input paramaeters
     *  @return void
     *  @author Sreekanth Dayanand
     *  @date 23rd June 2022
     *  @details 
		Outputs javascript to screen.
		1. declares variable 'ajaxurl'
		2. declares function 'refreshOSOLMultiCaptchaImage' for both gdpr and cookie type captchas
		
     */
	 function add_ajaxurl_cdata_to_front(){ 
	//dd();
	?>
		<script type="text/javascript"> //<![CDATA[
			var ajaxurl = '<?php echo admin_url( 'admin-ajax.php'); ?>';
			static_refresh_image = '<?php echo CUST_CAPTCHA_DIR_URL;?>/utils/refresh.gif';
			animated_refresh_image = '<?php echo CUST_CAPTCHA_DIR_URL;?>/utils/refresh-animated.gif';
			function getOSOLMultiCaptchaURL()
			{
				
				osolMultiCaptchaURL = '<?php echo admin_url( 'admin-ajax.php'); ?>?action=cccontact_display_captcha&rand='+ new Date().getTime();
				return osolMultiCaptchaURL;
			}
			<?php
			if($GLOBALS['OSOLMulticaptcha_gdprCompliantNoCookie'])
			{
			?>
			function refreshOSOLMultiCaptchaImage()
			{
				
				var captchaEncypted = document.querySelector("#OSOLmulticaptcha_captchaEncypted");
				var osolCaptchaImage = document.querySelector(".osol_cccaptcha");
				  if(captchaEncypted ==  null)
				  {
					  console.log("captchaEncypted is not seen.");
				  }
				  else
				  {
					  var time =  new Date().getTime();
					  var url = getOSOLMultiCaptchaURL();
					  var xhr = new XMLHttpRequest()
					  xhr.open('GET', url, true);
					  xhr.addEventListener('readystatechange', function(e) {
						if (xhr.readyState == 4 && xhr.status == 200) {
						  // Done. Inform the user
									 let xhrResponse = JSON.parse(xhr.responseText);
									  /* var captchaEncypted = document.querySelector("#OSOLmulticaptcha_captchaEncypted");
									  var osolCaptchaImage = document.querySelector(".osol_cccaptcha"); */
									  captchaEncypted.value = xhrResponse.captchaEncypted;
									  
									  osolCaptchaImage.src = "data:image/png;base64," + xhrResponse.imageContent;
						}
						else if (xhr.readyState == 4 && xhr.status != 200) {
						  // Error. Inform the user
						}
					  })
				 
					xhr.send(null)
				  }
			}
			<?php
			}
			else
			{?>
			function refreshOSOLMultiCaptchaImage(captchaInst)
			{	
				newURL = getOSOLMultiCaptchaURL()+'&OSOLmulticaptcha_inst='+captchaInst;
				//alert(jQuery('img.osol_cccaptcha')[0].src);
				jQuery('img.osol_cccaptcha')[captchaInst].src = newURL
			}
			<?php			
			}
			?>
		//]]> </script>
	<?php }
	/* <!-- wordpress hooks ends here --> */
	
	
	/* <!-- Helper Methods starts here --> */
	
	
		
	
	
	
	// helper methods
	/* protected function valid_email($str) {
        return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
	}
	protected function getCaptchaWithAjax() // which returns json with `captchaEncypted` & `imageContent`
	{
		$captcha = new \OSOLUtils\Helpers\OSOLmulticaptcha();
		$OSOLCaptchaEncryptionKey = get_option('OSOLCaptchaEncryptionKey');
		$captchaEncryptionKey = $OSOLCaptchaEncryptionKey;// IMPORTANT ****** YOU MUST SET A CUSTOM VALUE FOR YOUR SITE
		$captcha->setCaptchaEncryptionKey($captchaEncryptionKey);
		$returnImgObj = true;
		$captchaImgObj = $captcha->displayCaptcha($returnImgObj);
		ob_start();
		imagepng($captchaImgObj);
		$imageContent = ob_get_contents();
		ob_end_clean(); 
		
		$var2Display = new stdClass();
		$var2Display->captchaEncypted = $captcha->getEncryptedCaptchaString();
		$var2Display->imageContent = base64_encode($imageContent);
		die(json_encode($var2Display));
		
	}//protected function getCaptchaWithAjax() */ 
	
}//Class Backend{
?>