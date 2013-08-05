<script>
function cccontact_validate_ajax()
{
	var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if(!regex.test(jQuery("#cccontact_email").attr('value')))
	{
		alert("Invalid email "+jQuery("#cccontact_email").attr('value'));
		return false;
	}
	cccontact_post_data = {};
	//get each input value of form
	/*jQuery("form :input").each(function(){
		if(typeof jQuery(this).attr('name') != 'undefined')
		{
			cccontact_post_data[jQuery(this).attr('name')] = jQuery(this).attr('value');
			//alert(jQuery(this).attr('name') + " = " + jQuery(this).attr('value'));
		}
		
	});*/
	var checkVars = {cccontact_email:'Email',cccontact_name:'Name',cccontact_subject:'Subject',cccontact_message:'Message',OSOLmulticaptcha_keystring:'Captcha'};
	cccontact_post_data[jQuery("#cccontact_email").attr('name')] = jQuery(jQuery("#cccontact_email")).attr('value');
	cccontact_post_data[jQuery("#cccontact_name").attr('name')] = jQuery(jQuery("#cccontact_name")).attr('value');
	cccontact_post_data[jQuery("#cccontact_subject").attr('name')] = jQuery(jQuery("#cccontact_subject")).attr('value');
	cccontact_post_data[jQuery("#cccontact_message").attr('name')] = jQuery(jQuery("#cccontact_message")).attr('value');
	cccontact_post_data[jQuery("#OSOLmulticaptcha_keystring").attr('name')] = jQuery(jQuery("#OSOLmulticaptcha_keystring")).attr('value');
	for(var i in checkVars)
	{
		cccontact_post_data[jQuery("#"+i).attr('name')] = jQuery(jQuery("#"+i)).attr('value');
		if(cccontact_post_data[jQuery("#"+i).attr('name')] == '')
		{
			alert(checkVars[i] + ' cannot be blank');
			jQuery("#"+i).focus();
			return false;
		}
	}
	
	cccontact_post_data['action']= 'cccontact_validate_ajax';
	/*alert(ajaxurl);
	jQuery.post(
	   ajaxurl, //ajaxurl
	   cccontact_post_data, 
	   function(response){
		  alert('The server responded: ' + response);
	   }
	);*/
	jQuery("#cccontact_submit_loader").css("display", "block");
	//alert(ajaxurl);
	jQuery.ajax({
         type : "post",
         //dataType : json,
         url : ajaxurl,
         data : cccontact_post_data,//{action: "my_user_vote", post_id : post_id, nonce: nonce},
         success: function(response) {
			//alert(response);
			//response = jQuery.parseJSON('{"success":0,"message":"hi"}');
			//response = '{"success":0,"message":"Error: Incorrect CAPTCHA. Press your browser\'s back button and try again."}'
			response = jQuery.parseJSON(response);
            if(response.success == 1) {
                alert("Thanks!!Your mail has been send successfully.Our agent will reply you shortly.")
            }
            else {
               alert("Failed, "+ response.message)
            }
			jQuery("#cccontact_submit_loader").css("display", "none");
         }
      })   ;
	return false;
}
/*google.load("jquery", "1");
google.setOnLoadCallback(function(){*/
jQuery(document).ready(function() {
// Handler for .ready() called.
	jQuery('form#cccontact_form').attr('onsubmit', 'return cccontact_validate_ajax();');
	//alert('cccontact_form');
	
});
//});

</script>
<form id="cccontact_form" name="form1" method="post" action="">
  <p>
    <label>Your email :
      <input type="text" name="cccontact_email" value="<?php echo $_REQUEST["cccontact_email"];?>" id="cccontact_email" />
    </label>
  </p>
  <p>
    <label>Your Name :
      <input type="text" name="cccontact_name" value="<?php echo $_REQUEST["cccontact_name"];?>" id="cccontact_name" />
    </label>
  </p>
  <p>
    <label>Subject :
      <input type="text" name="cccontact_subject" value="<?php echo $_REQUEST["cccontact_subject"];?>" id="cccontact_subject" />
    </label>
  </p>
  <p>
    <label>Message :
      <br />
      <textarea name="cccontact_message"  id="cccontact_message" cols="45" rows="5"><?php echo $_REQUEST["cccontact_message"];?></textarea>
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
     <div id="cccontact_submit_loader" style="float:left;display:none"><img src="<?php echo CUST_CAPTCHA_DIR_URL;?>/utils/ajax-loader-big.gif" /></div>
      <input type="submit" name="cccontact_submit" id="cccontact_submit" value="Submit" />
     
    
  </p>
  <input type="hidden" name="cccontact_toMailSessionVar" value="<?php echo $toMailSessionVar; ?>" />
  <input type="hidden" name="cccontact_action" value="cust_captcha_contact_submit" />
</form>
