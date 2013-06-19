<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<form id="form1" name="form1" method="post" action="">
  <p>
    <label>Your email :
      <input type="text" name="cccontact_email" id="cccontact_email" />
    </label>
  </p>
  <p>
    <label>Your Name :
      <input type="text" name="cccontact_name" id="cccontact_name" />
    </label>
  </p>
  <p>
    <label>Subject :
      <input type="text" name="cccontact_subject" id="cccontact_subject" />
    </label>
  </p>
  <p>
    <label>Message :
      <br />
      <textarea name="cccontact_message" id="cccontact_message" cols="45" rows="5"></textarea>
    </label>
  </p>
  <p class="lost-form-captcha">
		<label for="captcha"><b><?php echo  __('Captcha', 'wpcaptchadomain');?> </b></label>
		<span class="required">*</span>
		<div style="clear:both;"></div>
		<?php echo cust_catcha_html();?>
		<div style="clear:both;"></div>
		<label for="OSOLmulticaptcha_keystring"><?php echo __('Type the text displayed above', 'wpcaptchadomain');?>:</label>
		<input id="OSOLmulticaptcha_keystring" name="OSOLmulticaptcha_keystring" size="15" type="text" />
		</p>
  <p>
    
      <input type="submit" name="cccontact_submit" id="cccontact_submit" value="Submit" />
    
  </p>
  <input type="hidden" name="cccontact_toMailSessionVar" value="<?php echo $toMailSessionVar; ?>" />
  <input type="hidden" name="cccontact_action" value="cust_captcha_contact_submit" />
</form>
</body>
</html>