/** @file
 *  @ingroup jsfiles
 *  @brief this file in jsfiles
 */
/** @addtogroup jsfiles
 *  
 *  Documentation of vanilla js functions.
 *  @{
 */ 
 
 
/**
 * Validates Contact Us form submission via AJAX
 * Validates email, captcha and other mandatory fields\n
 * Submits contact us form via AJAX
 * \fn boolean cccontact_validate_ajax()
 * \param none
 * \return boolean false if validation failed.
 */
function cccontact_validate_ajax()
{
 
	var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	var emailField = jQuery("#cccontact_email");
	//var emailSubmitted = jQuery("#cccontact_email").attr('value');
	var emailSubmitted = jQuery("#cccontact_email").val();
	/* console.log("emailField is " + emailField);
	console.log("emailSubmitted is " + emailSubmitted); */
	if(!regex.exec(emailSubmitted))
	{
		alert("Invalid email " + emailSubmitted);
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
	cccontact_post_data[jQuery("#cccontact_email").attr('name')] = jQuery(jQuery("#cccontact_email")).val();//.attr('value');
	cccontact_post_data[jQuery("#cccontact_name").attr('name')] = jQuery(jQuery("#cccontact_name")).val();//.attr('value');
	cccontact_post_data[jQuery("#cccontact_subject").attr('name')] = jQuery(jQuery("#cccontact_subject")).val();//.attr('value');
	cccontact_post_data[jQuery("#cccontact_message").attr('name')] = jQuery(jQuery("#cccontact_message")).val();//.attr('value');
	cccontact_post_data[jQuery("#OSOLmulticaptcha_keystring").attr('name')] = jQuery(jQuery("#OSOLmulticaptcha_keystring")).val();//.attr('value');
	cccontact_post_data[jQuery("#OSOLmulticaptcha_captchaEncypted").attr('name')] = jQuery(jQuery("#OSOLmulticaptcha_captchaEncypted")).val();//.attr('value');
	for(var i in checkVars)
	{
		//cccontact_post_data[jQuery("#"+i).attr('name')] = jQuery(jQuery("#"+i)).attr('value');
		cccontact_post_data[jQuery("#"+i).attr('name')] = jQuery(jQuery("#"+i)).val();
		if(cccontact_post_data[jQuery("#"+i).attr('name')] == '')
		{
			alert(checkVars[i] + ' cannot be blank');
			jQuery("#"+i).focus();
			return false;
		}
	}
	
	cccontact_post_data['action']= 'cccontact_validate_ajax';
	cccontact_post_data['cccontact_phone']= jQuery(jQuery("#cccontact_phone")).val();//.attr('value');
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
         url : ajaxurl,//set inside function add_ajaxurl_cdata_to_front() in custCaptchaContact.php
         data : cccontact_post_data,//{action: "my_user_vote", post_id : post_id, nonce: nonce},
         success: function(response) {
			
			//response = jQuery.parseJSON('{"success":0,"message":"hi"}');
			//response = '{"success":0,"message":"Error: Incorrect CAPTCHA. Press your browser\'s back button and try again."}'
			response = jQuery.parseJSON(response);//alert(response);
            if(response.success == 1) {
                alert("Thanks!!Your mail has been send successfully.Our agent will reply you shortly.")
				if(cccontact_form_in_tb_show)
				{
					 tb_remove();
				}
            }
            else {
               alert("Failed, "+ response.message);
			   //tb_remove();
            }
			jQuery("#cccontact_submit_loader").css("display", "none");
         }
      })   ;
	return false;
}
var cccontactFormAJAXified = false;

/**
 * Disables form submission button\n
 * Called in jQuery(document).ready\n
 * Submits contact us form via AJAX
 * \fn boolean ajaxifyCccontactForm()
 * \param none
 * \return void
 */
function ajaxifyCccontactForm(){
	 if(!cccontactFormAJAXified)
	{
		
		//jQuery('form#cccontact_form').attr('onsubmit', 'return cccontact_validate_ajax()');
		//for some reason the above line somestimes doesnt work
		/// \cond
		/* code that must be skipped by doxygen */
		jQuery('form#cccontact_form').unbind('submit').submit(function(e) {
															e.preventDefault();/* for IE8 */
															//alert("default prevented");
															return cccontact_validate_ajax()
															});
		
		//alert(jQuery('form#cccontact_form').attr('onsubmit'));
		
		cccontactFormAJAXified = true;
	}
}
/** @} */ // end of jsfiles


		/// \cond
		/* code that must be skipped by doxygen */
jQuery(document).ready(function() {
// Handler for .ready() called.
	ajaxifyCccontactForm();
	
});//jQuery(document).ready
/// \endcond
/// \cond

 /* code that must be skipped by doxygen */


if(cccontact_form_in_tb_show)//if called in thickbox
{
	//ajaxifyCccontactForm();
}//if(cccontact_form_in_tb_show)
/// \endcond