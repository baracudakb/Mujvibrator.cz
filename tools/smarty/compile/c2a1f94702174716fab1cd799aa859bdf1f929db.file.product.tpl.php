<?php /* Smarty version Smarty-3.0.7, created on 2012-06-21 01:52:56
         compiled from "/data/web/virtuals/24582/virtual/www/modules/mailalerts/product.tpl" */ ?>
<?php /*%%SmartyHeaderCode:10277147394fe26258f1b070-49482604%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c2a1f94702174716fab1cd799aa859bdf1f929db' => 
    array (
      0 => '/data/web/virtuals/24582/virtual/www/modules/mailalerts/product.tpl',
      1 => 1339512419,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10277147394fe26258f1b070-49482604',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>

<script type="text/javascript">
// <![CDATA[
oosHookJsCodeFunctions.push('oosHookJsCodeMailAlert');

function clearText() {
	if ($('#oos_customer_email').val() == '<?php echo smartyTranslate(array('s'=>'your@email.com','mod'=>'mailalerts'),$_smarty_tpl);?>
')
		$('#oos_customer_email').val('');
}

function oosHookJsCodeMailAlert() {
	$.ajax({
		type: 'POST',
		url: '<?php echo $_smarty_tpl->getVariable('base_dir')->value;?>
modules/mailalerts/mailalerts-ajax_check.php',
		data: 'id_product=<?php echo $_smarty_tpl->getVariable('id_product')->value;?>
&id_product_attribute='+$('#idCombination').val(),
		success: function (msg) {
			if (msg == '0') {
				$('#mailalert_link').show();
				$('#oos_customer_email').show();
			}
			else {
				$('#mailalert_link').hide();
				$('#oos_customer_email').hide();
			}
		}
	});
}

function  addNotification() {
	$.ajax({
		type: 'POST',
		url: '<?php echo $_smarty_tpl->getVariable('base_dir')->value;?>
modules/mailalerts/mailalerts-ajax_add.php',
		data: 'id_product=<?php echo $_smarty_tpl->getVariable('id_product')->value;?>
&id_product_attribute='+$('#idCombination').val()+'&customer_email='+$('#oos_customer_email').val()+'',
		success: function (msg) {
			if (msg == '1') {
				$('#mailalert_link').hide();
				$('#oos_customer_email').hide();
				$('#oos_customer_email_result').html("<?php echo smartyTranslate(array('s'=>'Request notification registered','mod'=>'mailalerts'),$_smarty_tpl);?>
");
				$('#oos_customer_email_result').css('color', 'green').show();
			}
			else {
				$('#oos_customer_email_result').html("<?php echo smartyTranslate(array('s'=>'Your e-mail address is invalid','mod'=>'mailalerts'),$_smarty_tpl);?>
");
				$('#oos_customer_email_result').css('color', 'red').show();
			}
		}
	});
	return false;
}

$(document).ready(function() {
	$('#oos_customer_email').bind('keypress', function(e) {
		if(e.keyCode == 13)
		{
			addNotification();
			return false;
		}
	});
});

//]]>
</script>

<!-- MODULE MailAlerts -->
<?php if (isset($_smarty_tpl->getVariable('email',null,true,false)->value)&&$_smarty_tpl->getVariable('email')->value){?>
	<input type="text" id="oos_customer_email" name="customer_email" size="20" value="<?php echo smartyTranslate(array('s'=>'your@email.com','mod'=>'mailalerts'),$_smarty_tpl);?>
" class="mailalerts_oos_email" onclick="clearText();" /><br />
<?php }?>
<a href="#" onclick="return addNotification();" id="mailalert_link"><?php echo smartyTranslate(array('s'=>'Notify me when available','mod'=>'mailalerts'),$_smarty_tpl);?>
</a>
<span id="oos_customer_email_result" style="display:none;"></span>
<!-- END : MODULE MailAlerts -->
