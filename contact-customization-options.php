<?php
defined('CUST_CAPTCHA_FOLDER') or die('Direct access not permitted');

?>
<script>
var pattern = /^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_.-])+\.([a-zA-Z])+([a-zA-Z])+/; 

function isEmailAddress(str) {

    //alert(str);
    return pattern.test(str);    

}
function confirm_email()
{
	if(!isEmailAddress(document.getElementById('OSOLMulticaptcha_contact_email').value))
	{
		alert("Enter  valid email");
		return false;
	}
	return true;
}
</script>
<form id="form1" name="form1" method="post" action="" onsubmit="return confirm_email();">
  <label>Contact Email
    <input type="text" name="cust_captcha_contact_email" id="OSOLMulticaptcha_contact_email" value="<?php echo get_option('cust_captcha_contact_email'); ?>" />
   
  </label> <br />
  <label>Submit
    <input type="submit" name="OSOLMulticaptcha_contact_button" id="OSOLMulticaptcha_contact_button" value="Submit" />
  </label>
  <input type="hidden" name="action" value="cust_captcha_contact_email_submit" />
</form>
