<script type="text/javascript" src="../modules/{$varShipping.moduleName}/js/shipping.js"></script>
<fieldset style="border: 0px;">
	<form onsubmit="enableSelect();" action="index.php?tab={$glob.tab}&configure={$glob.configure}&token={$glob.token}&tab_module={$glob.tab_module}&module_name={$glob.module_name}&id_tab=2&section=shipping" method="post" class="form" id="configFormShipping">		
		<h4>{l s='Shipping' mod='tntcarrier'} :</h4>
        <div class="margin-form"><input type="hidden" size="20" id="tnt_carrier_shipping_pex" name="tnt_carrier_shipping_pex" value="{$varShipping.pex}" /></div>
        <label>{l s='Company Name' mod='tntcarrier'} : </label>
		<div class="margin-form"><input type="text" size="20" id="tnt_carrier_shipping_company" name="tnt_carrier_shipping_company" value="{$varShipping.company}" /> <span style="color:red">*</span></div>
		<label>{l s='Address line 1' mod='tntcarrier'} : </label>
		<div class="margin-form"><input type="text" size="20" id="tnt_carrier_shipping_address1" name="tnt_carrier_shipping_address1" value="{$varShipping.address1}" /> <span style="color:red">*</span></div>
		<label>{l s='Address line 2' mod='tntcarrier'} : </label>
		<div class="margin-form"><input type="text" size="20" id="tnt_carrier_shipping_address2" name="tnt_carrier_shipping_address2" value="{$varShipping.address2}" /></div>
		<label>{l s='Postal Code' mod='tntcarrier'} : </label>
		<div class="margin-form"><input type="text" size="20" id="tnt_carrier_shipping_postal_code" name="tnt_carrier_shipping_postal_code" value="{$varShipping.zipCode}" onkeyup="displayCity({$glob.shop})"/> <span style="color:red">*</span> <span id="resultCity"></span></div>
		<label>{l s='City' mod='tntcarrier'} : </label>
		<div class="margin-form">
		<select disabled="disabled" id="tnt_carrier_shipping_city" name="tnt_carrier_shipping_city" style="width:130px" >
		{if $varShipping.city}<option value="{$varShipping.city}">{$varShipping.city}</option>{/if}
		</select>
		<span style="color:red">* {$soap}</span></div>
		<label>{l s='Company Closing Time' mod='tntcarrier'} : </label>
		<div class="margin-form"><input type="text" size="20" name="tnt_carrier_shipping_closing" value="{$varShipping.closing}" /> (HH:MM) <span style="color:red">*</span></div>
		<br/>
		<label>{l s='Contact last name' mod='tntcarrier'} : </label>
		<div class="margin-form"><input type="text" size="20" id="tnt_carrier_shipping_last_name" name="tnt_carrier_shipping_last_name" value="{$varShipping.lastName}" /> <span style="color:red">*</span></div>
		<label>{l s='Contact first name' mod='tntcarrier'} : </label>
		<div class="margin-form"><input type="text" size="20" id="tnt_carrier_shipping_first_name" name="tnt_carrier_shipping_first_name" value="{$varShipping.firstName}" /> <span style="color:red">*</span></div>
		<label>{l s='Contact Email Address' mod='tntcarrier'} : </label>
		<div class="margin-form"><input type="text" size="20" name="tnt_carrier_shipping_email" value="{$varShipping.email}" /> <span style="color:red">*</span></div>
		<label>{l s='Contact Phone Number' mod='tntcarrier'} : </label>
		<div class="margin-form"><input type="text" size="20" name="tnt_carrier_shipping_phone" value="{$varShipping.phone}" /> <span style="color:red">*</span></div><br/>
		<div style="padding-left:210px">
		<input type='checkbox' name='tnt_carrier_shipping_collect' {if $varShipping.collect} checked='checked'{/if}/> : {l s='Check this box if you are in on-demand pickup' mod='tntcarrier'}
		</div>
		<br/><br/>
		<span style="font-weight:bold">{l s='Label Format for printing (This Label will have to be sticked on the package)' mod='tntcarrier'} : </span><br/><br/>
        <div style="padding-left:210px">
		<select name="tnt_carrier_print_sticker">
			<option {if $varShipping.sticker == "STDA4"}selected="selected"{/if} value="STDA4">{l s='A4 printing' mod='tntcarrier'}</option>
			<option {if $varShipping.sticker == "THERMAL"}selected="selected"{/if} value="THERMAL">THERMAL</option>
			<option {if $varShipping.sticker == "THERMAL,NO_LOGO"}selected="selected"{/if} value="THERMAL,NO_LOGO">THERMAL {l s='without printing the logo TNT' mod='tntcarrier'}</option>
			<option {if $varShipping.sticker == "THERMAL,ROTATE_180"}selected="selected"{/if} value="THERMAL,ROTATE_180">THERMAL {l s='with a reverse print' mod='tntcarrier'}</option>
			<option {if $varShipping.sticker == "THERMAL,NO_LOGO,ROTATE_180"}selected="selected"{/if} value="THERMAL,NO_LOGO,ROTATE_180">THERMAL {l s='without printing the logo TNT and with a reverse print' mod='tntcarrier'}</option>
		</select></div><br/><br/>
		<div class="margin-form"><input class="button" name="submitSave" type="submit" value="{l s='save' mod='tntcarrier'}"></div>
	</form>
<span style="color:red">* : {l s='Required fields' mod='tntcarrier'}</span>
</fieldset>