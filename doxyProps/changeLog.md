# Change Log

## Stable Tag 0.9

### 08-10-2022

1. corrected bug in MVC/ExtraClasses/OSOLMultiCaptcha.php by appropriately setting `$this->fontPNGLocation`
```
$this->fontPNGLocation = realpath(dirname(__FILE__).$this->DS.'..'.$this->DS.'..'.$this->DS)
```
2. Fixed bug in MVC/Views/ContactusView.php adding a slash to stdClass
```
$var2Display = new \stdClass();
```
3. in custCaptchaContact.php changed 
```
	add_action( 'lostpassword_form', 'include_cust_captcha_lostpassword' );
	add_action( 'lostpassword_post', 'include_cust_captcha_lostpassword_post', 10, 3 );
```
to
```
	add_action( 'lostpassword_form', [$OSOLCCC_Frontend_inst,'include_cust_captcha_lostpassword'] );
	add_action( 'lostpassword_post', [$OSOLCCC_Frontend_inst,'include_cust_captcha_lostpassword_post'], 10, 3 );
```

4. in MVC/Views/ContactusView.php
added inside `function getCaptchaBlockHTML($forForm = "login")`
```
		$REQUEST_URI = filter_input(INPUT_SERVER, 'REQUEST_URI'); 
		//echo $REQUEST_URI . " pos is ". strpos( $REQUEST_URI, '/wp-login.php' ). "<br />";
		if( false !== strpos( $REQUEST_URI, '/wp-login.php' ) ) //in wp-login.php hooks ie "add_action( 'admin_footer', & add_action( 'wp_footer'," down work since there is no  <footer>
		{ 
			$OSOLCCC_CommonClass_inst = \OSOLCCC\Hooks\Common::getInstance();
			$OSOLCCC_CommonClass_inst->add_ajaxurl_cdata_to_front();
			$OSOLCCC_CommonClass_inst->add_ccc_onload();
		}
```
5. in MVC/Hooks/Common.php changed `function refreshOSOLMultiCaptchaImage()` so ajax call for captcha image is made only when required
## Stable Tag 0.8

### Made more OOP

### 24-06-2022

*Objective:* 
seperate logic for plugin (further seperate front end and backend) & wp hooks

1. created new folder `Helpers` for plugnin helper classes , name space will be `namespace OSOLCCCF\Helpers`
2. used `spl_autoload_register` to [autoload](https://www.php.net/manual/en/function.spl-autoload-register.php)
```
// auto load Helpers/Frontend.php while calling new \OSOLCCC\Helpers\Frontend() 
if(!function_exists('version_compare') || version_compare(phpversion(), '5.1.0', '<'))die("Minimum version required for 'Customizable Captcha and contact us' plugin is 5.1.0");
spl_autoload_register(function ($class) {
	// project-specific namespace prefix
	$prefix = 'OSOLCCC\\Helpers\\';	
	// base directory for the namespace prefix
	$base_dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'Helpers/';
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
	if (file_exists($mappedFile)) {
		require $mappedFile;
	}
	else
	{
		die('<p style="background:#f00">ERROR!!!!! file : '.$mappedFile. " does not exist to autoload for ".$class ."</p>");
	}
});
```
2. created new `FrontEnd` , `Backend` and `Wphooks` classes in `Helpers` folder
5. declared the following in the beginning.
```
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'pluginClasses/FrontEnd.php');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'pluginClasses/BackEnd.php');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'pluginClasses/WPHooks.php');
$OSOL_CCC_HandlerFrontEnd_inst = new \OSOLCCCF\PluginHelpers\FrontEndMain();
$OSOL_CCC_HandlerBackEnd_inst = new \OSOLCCCF\PluginHelpers\BackEndMain();
$OSOL_CCC_HandlerWPHooks_inst = new \OSOLCCCF\PluginHelpers\WPHooks();
```
6. replaced all add_action\('([^']+)',\s*'([^']+)'   with add_action\('\1',[$OSOL_CCC_HandlerFrontEnd_inst,'\2'] 
7. Main file `custCaptchaContact.php` only contains calls to wp hooks




## Stable Tag 0.7

### Made Captcha GDPR Compliant

### 22-06-2022

1. OSOLMultiCaptcha.php replaced with latest [OSOLMultiCaptcha](https://github.com/osolgithub/OSOLMulticaptcha)
2. captcha-customization-options.php
	1. 
	
	<li>
		<label id="jform_params_caseInsensitive-lbl" for="jform_params_caseInsensitive" title="">GDPR Compliant(No Cookie):</label><input type="checkbox" id="jform_params_gdprCompliantNoCookie" name="OSOLMulticaptcha_gdprCompliantNoCookie" value="1" <?php echo get_option('OSOLMulticaptcha_gdprCompliantNoCookie') == 1?"checked=\"checked\"":'';?>/>
	</li>
	
	2. 
	
	<li>
		<?php $OSOLCaptchaEncryptionKey = get_option('OSOLCaptchaEncryptionKey') != ''?get_option('OSOLCaptchaEncryptionKey'):"YourUniqueEncryptionKey";?>
		<label id="jform_params_bgColor-lbl" for="jform_params_bgColor" title="">Encryption Key</label>
		<input name="OSOLCaptchaEncryptionKey" id="jform_params_bgColor" value="<?php echo $OSOLCaptchaEncryptionKey;?>" size="25" type="text" />
    </li>
  
3. displayCaptcha.php: 
	1. added following blocks to prevent setting $_SESSION
	
	```
	$OSOLMulticaptcha_gdprCompliantNoCookie = get_option('OSOLMulticaptcha_gdprCompliantNoCookie');
	if(!$OSOLMulticaptcha_gdprCompliantNoCookie)
	{
	```
	
	and following block to show json object instead of image
	
	```
		if(!$OSOLMulticaptcha_gdprCompliantNoCookie)
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
	```	
	

4. custCaptchaContact.php added
	1. added
	```
	if(!isset($_REQUEST['OSOLMulticaptcha_gdprCompliantNoCookie']))
	{
		update_option('OSOLMulticaptcha_gdprCompliantNoCookie',0);
	}
	```
	
    2. added `<input type="hidden" name="OSOLmulticaptcha_captchaEncypted" id="OSOLmulticaptcha_captchaEncypted" value="" >` near each `<input id="OSOLmulticaptcha_keystring" name="OSOLmulticaptcha_keystring" size="15" type="text" />'
	3. 
	```
	$GLOBALS['OSOLMulticaptcha_gdprCompliantNoCookie'] = get_option('OSOLMulticaptcha_gdprCompliantNoCookie');
	```
	
	so that the following way it can be checked if captcha is without cookie
	
	```
	if($GLOBALS['OSOLMulticaptcha_gdprCompliantNoCookie']) 
	```
	
	5. Added new `function refreshOSOLMultiCaptchaImage()` to show captcha based on json data
	6. Added `add_action( 'wp_footer', 'add_ccc_onload' );` to call refreshOSOLMultiCaptchaImage() on window.onload.
	7. Inside `include_cust_captcha_login()` added `add_ccc_onload();` since there is no  <footer> 
	4. inside `function cust_captcha_contact_validate_and_mail()`. added code to verify encrypted captcha
	
4. cccontact_form.php added 
    1. `<input type="hidden" name="OSOLmulticaptcha_captchaEncypted" id="OSOLmulticaptcha_captchaEncypted" value="" >` near  `<input id="OSOLmulticaptcha_keystring" name="OSOLmulticaptcha_keystring" size="15" type="text" />'
5. cccontact.js added 
	1. `cccontact_post_data[jQuery("#OSOLmulticaptcha_captchaEncypted").attr('name')] = jQuery(jQuery("#OSOLmulticaptcha_captchaEncypted")).val();`
	

