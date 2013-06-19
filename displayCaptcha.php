<?php
defined('CUST_CAPTCHA_FOLDER') or die('Direct access not permitted');
session_start();
		require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'OSOLmulticaptcha.php');
		$captcha = new OSOLmulticaptcha();
if(isset($_REQUEST['previewCaptcha']) && $_REQUEST['previewCaptcha'] == 'True')
{
	if (!is_admin()) require_once(ABSPATH . 'wp-includes/pluggable.php');
	if(!is_super_admin()){die('should be logged in as admin to view captcha preview');}
}
else
{
			$captcha->imageFunction = 'create_image'.get_option('cust_captcha_imageFunction','Adv');
			$captcha->font_size = (int)get_option('cust_captcha_letterSize',$captcha->font_size);
			$captcha->font_ttf  = $captcha->fontPNGLocation.$captcha->DS.'ttfs'.$captcha->DS.(get_option('cust_captcha_fontFile','AdLibBT.TTF'));
			$captcha->bgColor = get_option('cust_captcha_bgColor',$captcha->bgColor);
			$captcha->textColor = get_option('cust_captcha_textColor',$captcha->textColor);
			
			$captcha->symbolsToUse = get_option('cust_captcha_allowedSymbols',$captcha->symbolsToUse);
			//$captcha->fluctuation_amplitude = 4;//changing this creates unexpected issues
			$captcha->white_noise_density = get_option('cust_captcha_white_noise_density',$captcha->white_noise_density);
			$captcha->black_noise_density = get_option('cust_captcha_black_noise_density',$captcha->black_noise_density);
			//die("<pre>".print_r($captcha,true)."</pre>");
}
		
		$captcha->displayCaptcha();
		$_SESSION['OSOLmulticaptcha_keystring'] = $captcha->keystring;
    

?>