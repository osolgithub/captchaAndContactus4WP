<?php
/**
* @class OSOLCCC::Views::ContactusView

*  @brief \OSOLCCC\Views\ContactusView: Used for handling Contact us functionality
*  @details  
 This class is the view class for contact us module. \n
 
\par instantiation 
\OSOLCCC\Views\ContactusView::getInstance() 
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



*  @date 23rd June 2022

 */
namespace OSOLCCC\Views;
class ContactusView extends \OSOLCCC\SingletonParent{
	/**
     *  @brief getCaptchaBlockHTML : to get HTML for showing the entire captcha block. 
     *
     *  @param [in] string $forForm
     *  @return string HTML for showing the entire captcha block
     *  @author Sreekanth Dayanand
     *  @date 23rd June 2022
     *  @details 
		returns HTML for showing the entire captcha block. Called in login,register,comment,post forms from \OSOLCCC\Hooks\Frontend
     */
	function getCaptchaBlockHTML($forForm = "login")
	{
		$REQUEST_URI = filter_input(INPUT_SERVER, 'REQUEST_URI'); 
		//echo $REQUEST_URI . " pos is ". strpos( $REQUEST_URI, '/wp-login.php' ). "<br />";
		if( false !== strpos( $REQUEST_URI, '/wp-login.php' ) ) //in wp-login.php hooks ie "add_action( 'admin_footer', & add_action( 'wp_footer'," down work since there is no  <footer>
		{ 
			$OSOLCCC_CommonClass_inst = \OSOLCCC\Hooks\Common::getInstance();
			$OSOLCCC_CommonClass_inst->add_ajaxurl_cdata_to_front();
			$OSOLCCC_CommonClass_inst->add_ccc_onload();
		}
		return  '<p class="' . $forForm . '-form-captcha">
				<label for="captcha"><b>'. __('Captcha', 'osolwpccc').' </b></label>
				<span class="required">*</span>
				<div style="clear:both;"></div>
				'.$this->cust_catcha_html().'
				<div style="clear:both;"></div>
				<label for="OSOLmulticaptcha_keystring">'.__('Type the text displayed above', 'osolwpccc').':</label>
				<input id="OSOLmulticaptcha_keystring" name="OSOLmulticaptcha_keystring" size="15" type="text" tabindex="30" />
				<input type="hidden" name="OSOLmulticaptcha_captchaEncypted" id="OSOLmulticaptcha_captchaEncypted" value="" >
				</p>';
	}//function getCaptchaBlockHTML($forForm = "login")
	/**
     *  @brief getCaptchaBlockHTML : to get HTML for showing the entire captcha block. 
     *
     *  @param [in] no input paramaeters
     *  @return string HTML for showing captch image
     *  @author Sreekanth Dayanand
     *  @date 23rd June 2022
     *  @details 
		returns HHTML for showing captch image. Called in \OSOLCCC\Views\ContactusView->getCaptchaBlockHTML
     */
	function cust_catcha_html()
	{
		if(!isset($GLOBALS['OSOLMultiCaptcha_inst']))
		{
			//echo "HI cust_catcha_html";
			$GLOBALS['OSOLMultiCaptcha_inst'] = -1;			 
		}
		$GLOBALS['OSOLMultiCaptcha_inst']++;
			return '<a class="osol_cccaptcha_a" href="http://www.outsource-online.net/osolmulticaptcha-simplest-php-captcha-for-html-forms.html"><img class="osol_cccaptcha" src="'.admin_url( 'admin-ajax.php').'?action=cccontact_display_captcha&rand='.rand().'&OSOLmulticaptcha_inst='.$GLOBALS['OSOLMultiCaptcha_inst'].'" /></a> &nbsp;<a href="javascript:refreshOSOLMultiCaptchaImage('.$GLOBALS['OSOLMultiCaptcha_inst'].');"><img src="'.CUST_CAPTCHA_DIR_URL.'/utils/refresh.gif" onmouseover="this.src=animated_refresh_image" onmouseout="this.src=static_refresh_image" title="Refresh" /></a> ';
	}//function cust_catcha_html()
	/**
     *  @brief cccontact_tb_show_call : to show contact us form in thickbox modal, use the following line
     *
     *  @param [in] $cccontact_lightbox_title
     *  @param [in] $cccontact_subject
     *  @param [in] $cccontact_message
     *  @return string Javascript function 'tb_show(' with required parameters
     *  @author Sreekanth Dayanand
     *  @date 23rd June 2022
     *  @details 
		For getting javascript function to show contactus form inside thickbox
		<a href="javascript:<?php echo \OSOLCCC\Views\ContactusView::getInstance()->cccontact_tb_show_call("hello","test Subject");?>">Show Contact us In Modal</a>
     */
	
	function cccontact_tb_show_call($cccontact_lightbox_title,$cccontact_subject="",$cccontact_message="")
	{
		$url = admin_url( 'admin-ajax.php')."?action=cccontact_tb_show_modal&cccontact_subject=".urlencode($cccontact_subject)."&cccontact_message=".urlencode($cccontact_message)."&rand=".rand();
		$url .= "&height=575&width=650";
		return "tb_show('$cccontact_lightbox_title','$url','')";
	}//function cccontact_tb_show_call($cccontact_lightbox_title,$cccontact_subject="",$cccontact_message="")
	
	/**
     *  @brief displayCaptcha : outputs captcha Image
     *
     *  @param [in]  no input paramaeters
     *  @return void
     *  @author Sreekanth Dayanand
     *  @date 23rd June 2022
     *  @details 
		outputs captcha Image. Called from \OSOLCCC\Hooks\Common->cust_captcha_display_captcha()
     */
	function displayCaptcha(){
		$captcha = \OSOLCCC\ExtraClasses\OSOLmulticaptcha::getInstance();
		$OSOLMulticaptcha_gdprCompliantNoCookie = get_option('OSOLMulticaptcha_gdprCompliantNoCookie');
		if(!$OSOLMulticaptcha_gdprCompliantNoCookie)
		{
			session_start();
			if(!isset($_SESSION['OSOLmulticaptcha_keystring']) ||  !is_array($_SESSION['OSOLmulticaptcha_keystring']))
			{
				
				$_SESSION['OSOLmulticaptcha_keystring'] =  array();// make it an array to avoid clash when there are ,ultiple captchas in same page,eg:comment form and contact us form
			}
		}//if($OSOLMulticaptcha_gdprCompliantNoCookie)
			$previewCaptcha = isset($_REQUEST['previewCaptcha']) && $_REQUEST['previewCaptcha'] == 'True';
		if($previewCaptcha)
		{
			if (!is_admin()) require_once(ABSPATH . 'wp-includes/pluggable.php');
			if(!is_super_admin()){die('should be logged in as admin to view captcha preview');}
			$captcha->displayCaptcha();
			exit;
		}
		//else
		{
					
			$captcha->imageFunction = 'create_image'.get_option('OSOLMulticaptcha_imageFunction','Adv');
			$captcha->font_size = (int)get_option('OSOLMulticaptcha_letterSize',$captcha->font_size);
			$captcha->font_ttf  = $captcha->fontPNGLocation.$captcha->DS.'ttfs'.$captcha->DS.(get_option('OSOLMulticaptcha_fontFile','AdLibBT.TTF'));
			$captcha->bgColor = get_option('OSOLMulticaptcha_bgColor',$captcha->bgColor);
			$captcha->textColor = get_option('OSOLMulticaptcha_textColor',$captcha->textColor);
			
			$captcha->symbolsToUse = get_option('OSOLMulticaptcha_allowedSymbols',$captcha->symbolsToUse);
			//$captcha->fluctuation_amplitude = 4;//changing this creates unexpected issues
			$captcha->white_noise_density = get_option('OSOLMulticaptcha_white_noise_density',$captcha->white_noise_density);
			$captcha->black_noise_density = get_option('OSOLMulticaptcha_black_noise_density',$captcha->black_noise_density);
			//die("<pre>".print_r($captcha,true)."</pre>");
		}
	
		//die(get_option('OSOLMulticaptcha_gdprCompliantNoCookie'));
		if($OSOLMulticaptcha_gdprCompliantNoCookie)
		{
			$captchaEncryptionKey = get_option('OSOLCaptchaEncryptionKey');// IMPORTANT ****** YOU MUST SET A CUSTOM VALUE FOR YOUR SITE from admin panel
			$captcha->setCaptchaEncryptionKey($captchaEncryptionKey);
			$returnImgObj = true;
			$captchaImgObj = $captcha->displayCaptcha($returnImgObj);
			ob_start();
			imagepng($captchaImgObj);
			$imageContent = ob_get_contents();
			ob_end_clean(); 
			
			$var2Display = new \stdClass();
			$var2Display->captchaEncypted = $captcha->getEncryptedCaptchaString();
			$var2Display->imageContent = base64_encode($imageContent);
			die(json_encode($var2Display));
			
		}
		else
		{
			$keystring = $captcha->displayCaptcha();
			if(isset($_REQUEST['OSOLmulticaptcha_inst']) && $_REQUEST['OSOLmulticaptcha_inst'] != '')
			{
				$_SESSION['OSOLmulticaptcha_keystring'][$_REQUEST['OSOLmulticaptcha_inst']] = $keystring;//
			}
			else
			{
				$_SESSION['OSOLmulticaptcha_keystring'][] = $keystring;//$captcha->keystring;
			}
			if(count($_SESSION['OSOLmulticaptcha_keystring']) >3)//maximum 3 captcha allowed per page otherwise bots will misuse
			{
				$_SESSION['OSOLmulticaptcha_keystring'] = array_slice($_SESSION['OSOLmulticaptcha_keystring'],-3);
			}
		}
	}//function displayCaptcha()
}//Class ContactusView extends \OSOLCCC\SingletonParent{
	?>