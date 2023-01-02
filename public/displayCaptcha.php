<?php
/**
This file is no longer needed.
The functionality of this file is moved to  OSOLCCC\Views\ContactusView->displayCaptcha()
*/
defined('CUST_CAPTCHA_FOLDER') or die('Direct access not permitted');

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'OSOLmulticaptcha.php');
$captcha = new OSOLmulticaptcha();
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
			
			$var2Display = new stdClass();
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
		
    

?>