<?php
defined('CUST_CAPTCHA_FOLDER') or die('Direct access not permitted');

require_once('OSOLmulticaptcha.php');
$captcha = new OSOLmulticaptcha();
//$captcha->displayCaptcha();

?>
<script>
						function previewOSOLCaptcha(e,text){
							var image = new Image();
							image.onload = function() { // always fires the event.
								document.getElementById('ToolTip').innerHTML=  text;
							};
							image.src = getOSOSLCaptchaPreviewImageURL();
							//alert(e.clientX+20+document.body.scrollLeft + \" : \" + e.clientY+document.body.scrollTop);
							ajaxLoaderURL = currentURLPath + 'utils/ajax-loader-big.gif';
							document.getElementById('ToolTip').innerHTML=  '<div><img src=\"'+ajaxLoaderURL+'\" style=\"float:left\" /></div><div>Please wait while captcha preview is loaded</div>';
							
							ToolTip.style.visibility="visible";
							//alert(e.clientY);
							ToolTip.style.left = (e.clientX)+'px';
							ToolTip.style.top = (e.clientY-($('jform_params_letterSize').value * 3)) + 'px';
							//alert(ToolTip.style.left  + \" : \"  + ToolTip.style.top  + ' : ' + ToolTip.style.visibility);
						}
						function hidePreviewOSOLCaptcha(){
							ToolTip.style.visibility="hidden";
						}
						function $(elementId)
						{
							var elementObj = document.getElementById(elementId);
							//alert(elementObj.id + " : " +elementObj.type);
							if(elementObj.type != 'select-one')
							{
								return elementObj;
							}
							else
							{
								return elementObj.options[elementObj.selectedIndex]
							}
						}
						function getOSOSLCaptchaPreviewImageURL()
						{
							formFieldPrefix = 'jform_params_'
							var formFields = {bgColor:'osolcaptcha_bgColor',textColor:'osolcaptcha_textColor',allowedSymbols:'osolcaptcha_symbolsToUse',imageFunction:'osolcaptcha_imageFunction',fontFile:'osolcaptcha_font_ttf',white_noise_density:'white_noise_density',black_noise_density:'black_noise_density',letterSize:'osolcaptcha_font_size'}
						
							qVars = 'previewCaptcha=True&';
							for(var i in formFields)
							{
								qVars = qVars +formFields[i]+'='+encodeURIComponent($(formFieldPrefix+i).value)+'&';
							}
							return imageURL = '<?php echo admin_url( 'admin-ajax.php'); ?>?action=cccontact_display_captcha&'+qVars;;
						}
						function OSOLCaptchPreviewHTML()
						{
							imageURL = getOSOSLCaptchaPreviewImageURL();
							liveCaptchaURL = imageURL.replace('previewCaptcha=True&','');
							
						
							return  '<img src="'+imageURL+'" style=\"float:left\" />';
						}
						var currentURLPath = "<?php echo CUST_CAPTCHA_DIR_URL?>";
						</script>
						<style>
						#ToolTip {
						  position:fixed;
						 
						  visibility:hidden;
						   z-index:10000;
						  background-color:#dee7f7;
						  border:1px solid #337;
						  width:auto; padding:4px;
						  height:auto;
						  color:#000; font-size:11px; line-height:1.3;
						  font-family:verdana;
						}
						</style>
                        <form id="form1" name="form1" method="post" action="">
                        <ul>
  <li>
  	<?php $OSOLMulticaptcha_bgColor = get_option('OSOLMulticaptcha_bgColor') != ''?get_option('OSOLMulticaptcha_bgColor'):"#2c8007";?>
    <label id="jform_params_bgColor-lbl" for="jform_params_bgColor" title="">Background Color</label>
    <input name="OSOLMulticaptcha_bgColor" id="jform_params_bgColor" value="<?php echo $OSOLMulticaptcha_bgColor;?>" size="25" type="text" />
  </li>
</ul>
<ul>
  <li>
  	<?php $OSOLMulticaptcha_textColor = get_option('OSOLMulticaptcha_textColor') != ''?get_option('OSOLMulticaptcha_textColor'):"#ffffff";?>
    <label id="jform_params_textColor-lbl" for="jform_params_textColor" title="">Text Color</label>
    <input name="OSOLMulticaptcha_textColor" id="jform_params_textColor" value="<?php echo $OSOLMulticaptcha_textColor;?>" size="25" type="text" />
  </li>
</ul>
<ul>
  <li>
  <?php $OSOLMulticaptcha_white_noise_density = get_option('OSOLMulticaptcha_white_noise_density') != ''?get_option('OSOLMulticaptcha_white_noise_density'):"0";?>
    <label id="jform_params_white_noise_density-lbl" for="jform_params_white_noise_density" title="">Noise in BG color</label>
    <select name="OSOLMulticaptcha_white_noise_density" id="jform_params_white_noise_density">
    <option  selected="selected" value="<?php echo $OSOLMulticaptcha_white_noise_density;?>"><?php echo $OSOLMulticaptcha_white_noise_density;?></option>
    <option value="0">0</option>
	<option value="0.1">0.1</option>
	<option value="0.2">0.2</option>
	<option value="0.3">0.3</option>
</select>
  </li>
</ul>
<ul>
  <li>
  	<?php $OSOLMulticaptcha_black_noise_density = get_option('OSOLMulticaptcha_black_noise_density') != ''?get_option('OSOLMulticaptcha_black_noise_density'):"0";?>
    <label id="jform_params_black_noise_density-lbl" for="jform_params_black_noise_density" title="">Noise in Text color</label>
    <select name="OSOLMulticaptcha_black_noise_density" id="jform_params_black_noise_density">
	<option  selected="selected" value="<?php echo $OSOLMulticaptcha_black_noise_density;?>"><?php echo $OSOLMulticaptcha_black_noise_density;?></option>
    <option value="0">0</option>
	<option value="0.1">0.1</option>
	<option value="0.2">0.2</option>
	<option value="0.3">0.3</option>
</select>
  </li>
</ul>
<?php $OSOLMulticaptcha_fontFile = get_option('OSOLMulticaptcha_fontFile') != ''?get_option('OSOLMulticaptcha_fontFile'):$captcha->font_ttf;?>
<?php
		    $defaultFont = $OSOLMulticaptcha_fontFile;//$captcha->font_ttf;//'AdLibBT.TTF';
			$ttfPath =dirname(__FILE__)."/utils/ttfs"."/";
			$ttfsAvailable = "";
			if ($handle = opendir($ttfPath)) {
				
			
				
				while (false !== ($entry = readdir($handle))) {
					if(preg_match("@.*\.(ttf|otf)@i",$entry))
					{
						$selected = "";
						if($defaultFont == $entry)
						{
							$selected = " selected=\"selected\"";
						}
						$ttfsAvailable .=  "<option value=\"".$entry."\" $selected>".$entry."</option>\n";	
					}
				}
			
				
			
				closedir($handle);
			}
			if($ttfsAvailable != ''){
		?>
<ul>
  <li>
  	<?php $OSOLMulticaptcha_allowedSymbols = get_option('OSOLMulticaptcha_allowedSymbols') != ''?get_option('OSOLMulticaptcha_allowedSymbols'):$captcha->symbolsToUse;?>

    <label id="jform_params_allowedSymbols-lbl" for="jform_params_allowedSymbols" title="">Allowed Symbols</label>
    <input name="OSOLMulticaptcha_allowedSymbols" id="jform_params_allowedSymbols" value="<?php echo $captcha->symbolsToUse;?>" size="50" type="text" />
  </li>
</ul>
<ul>
  <li>
  <?php $OSOLMulticaptcha_imageFunction = get_option('OSOLMulticaptcha_imageFunction') != ''?get_option('OSOLMulticaptcha_imageFunction'):'Adv';?>
    <label id="jform_params_imageFunction-lbl" for="jform_params_imageFunction" title="">Select Letter Type</label>
    <select name="OSOLMulticaptcha_imageFunction" id="jform_params_imageFunction" class="" aria-invalid="false">
	<option selected="selected" value="<?php echo $OSOLMulticaptcha_imageFunction;?>"><?php echo $OSOLMulticaptcha_imageFunction=='Adv'?'Distorted letters':'Plane letters';?></option>
    <option value="Plane">Plane letters</option>
	<option  value="Adv">Distorted letters</option>
</select>
  </li>
</ul>

<ul>
  <li>
    <label id="jform_params_fontFile-lbl" for="jform_params_fontFile" title="">Select font</label>
    <select id="jform_params_fontFile" name="OSOLMulticaptcha_fontFile" class="" aria-invalid="false">
		
  <?php echo $ttfsAvailable;?>
	</select>
  </li>
</ul>
<ul>
  <li>
  	<?php $OSOLMulticaptcha_letterSize = get_option('OSOLMulticaptcha_letterSize') != ''?get_option('OSOLMulticaptcha_letterSize'):36;?>
    <label id="jform_params_letterSize-lbl" for="jform_params_letterSize" title="">Select letter size</label>
    <select name="OSOLMulticaptcha_letterSize" id="jform_params_letterSize">
	<option selected="selected" value="<?php echo $OSOLMulticaptcha_letterSize;?>"><?php echo $OSOLMulticaptcha_letterSize;?></option>
    <option value="24">24</option>
	<option  value="36">36</option>
	<option value="48">48</option>
	<option value="72">72</option>
</select>
  </li>
  <li><label id="jform_params_caseInsensitive-lbl" for="jform_params_caseInsensitive" title="">Case Insensitive Checking:</label><input type="checkbox" id="jform_params_caseInsensitive" name="OSOLMulticaptcha_caseInsensitive" value="1" <?php echo get_option('OSOLMulticaptcha_caseInsensitive') == 1?"checked=\"checked\"":'';?>/></li>
</ul>
<?php }
else
{
	
	?>
  <ul>
        <li>
       <h1> To use more options, save required fonts(.TTF/.OTF)s in the folder utils/ttfs</h1>
        Adanced options available with ttfs are 
        <ol>
            <li>Letter Type (Plane or distorted)</li>
            <li>Allowed symbols</li>
            <li>Font</li>
            <li>Letter size</li>
        </ol>
        <input type="hidden" id="jform_params_imageFunction" name="OSOLMulticaptcha_imageFunction" value="<?php echo $captcha->imageFunction;?>" />
        <input type="hidden" id="jform_params_allowedSymbols" name="OSOLMulticaptcha_allowedSymbols" value="<?php echo $captcha->symbolsToUse;?>" />
        <input type="hidden" id="jform_params_fontFile"  name="OSOLMulticaptcha_fontFile" value="<?php echo $defaultFont;?>" />
        <input type="hidden" id="jform_params_letterSize"  name="OSOLMulticaptcha_letterSize" value="<?php echo $captcha->font_size;?>" />
       </li>
      </ul>
    
    <?php
}
?>


<ul>
  <li>
    <label id="jform_params__-lbl" for="jform_params__">Preview Captcha</label>
    <div id="ToolTip"></div>
    <span onmouseover="javascript:previewOSOLCaptcha(event,OSOLCaptchPreviewHTML())" onmouseout="javascript:hidePreviewOSOLCaptcha()"> Hover Mouse here to preview Captcha with entered settings </span></li>
</ul>
                        
                         <input name="Submit" type="submit" value="Save Options" />
                         <input type="hidden" name="action" value="cust_captcha_options_submit" />
</form>