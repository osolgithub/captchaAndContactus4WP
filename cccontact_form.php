<?php 
$output_string = "
<script \">
var cccontact_form_in_tb_show = ".(isset($_REQUEST['action']) && $_REQUEST['action']=='cccontact_tb_show_modal'?'true':'false').";
</script>
<script src=\"".plugins_url("/js/cccontact.js",__FILE__)."\"></script>
<link rel=\"stylesheet\"  type=\"text/css\" media=\"screen, projection\" href=\"".plugins_url("/css/cccontact.css",__FILE__)."\"></link>
<form id=\"cccontact_form\" name=\"form1\" method=\"post\" action=\"\">
  <p>
    <label>Your email :
      <input  type=\"text\" name=\"cccontact_email\" value=\"{$_REQUEST['cccontact_email']}\" id=\"cccontact_email\" />
    </label><br /><br />
  
    <label>Your Name :
      <input type=\"text\" name=\"cccontact_name\" value=\"{$_REQUEST['cccontact_name']}\" id=\"cccontact_name\" />
    </label><br /><br />
  
    <label>Phone:
      <input type=\"text\" name=\"cccontact_phone\" value=\"{$_REQUEST['cccontact_phone']}\" id=\"cccontact_phone\" />
    </label>
  </p>
  <p>
    <label>Subject :
      <input type=\"text\" name=\"cccontact_subject\" value=\"{$_REQUEST['cccontact_subject']}\" id=\"cccontact_subject\" />
    </label>
  </p>
  <p>
    <label>Message :
      <br />
      <textarea name=\"cccontact_message\"  id=\"cccontact_message\" cols=\"45\" rows=\"5\">{$_REQUEST['cccontact_message']}</textarea>
    </label>
  </p>
  <div class=\"lost-form-captcha\">
		<label for=\"captcha\"><b>". __('Captcha', 'wpcaptchadomain')." </b></label>
		<span class=\"required\">*</span>
		<div style=\"clear:both;\"></div>
		".\OSOLCCC\Views\ContactusView::getInstance()->cust_catcha_html().'<a href="javascript:'.\OSOLCCC\Views\ContactusView::getInstance()->cccontact_tb_show_call("hello","test Subject").'">Show Contact us In Modal</a>'."
        
		<div style=\"clear:both;\"></div>
		<label for=\"OSOLmulticaptcha_keystring\">".__('Type the text displayed above', 'wpcaptchadomain').":</label>".
		'<input id="OSOLmulticaptcha_keystring" name="OSOLmulticaptcha_keystring" size="15" type="text" />'.'<input type="hidden" name="OSOLmulticaptcha_captchaEncypted" id="OSOLmulticaptcha_captchaEncypted" value="" >'."
		</p>
  <div>
     <div id=\"cccontact_submit_loader\" style=\"float:left;display:none\"><img src=\"".CUST_CAPTCHA_DIR_URL."/utils/ajax-loader-big.gif\" /></div>
      <input type=\"submit\" name=\"cccontact_submit\" id=\"cccontact_submit\" value=\"Submit\" />
    
    
  </p>
  <input type=\"hidden\" name=\"cccontact_toMailSessionVar\" value=\"{$toMailSessionVar}\" />
  <input type=\"hidden\" name=\"cccontact_action\" value=\"cust_captcha_contact_submit\" />
</form>
";
?>