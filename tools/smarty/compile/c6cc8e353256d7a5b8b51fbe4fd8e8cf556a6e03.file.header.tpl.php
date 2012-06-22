<?php /* Smarty version Smarty-3.0.7, created on 2012-06-21 04:05:26
         compiled from "/data/web/virtuals/24582/virtual/www/modules/ganalytics/header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:19970454324fe281668a5904-87299373%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c6cc8e353256d7a5b8b51fbe4fd8e8cf556a6e03' => 
    array (
      0 => '/data/web/virtuals/24582/virtual/www/modules/ganalytics/header.tpl',
      1 => 1339512322,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19970454324fe281668a5904-87299373',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', '<?php echo $_smarty_tpl->getVariable('ganalytics_id')->value;?>
']);
// Recommanded value by Google doc and has to before the trackPageView
_gaq.push(['_setSiteSpeedSampleRate', 5]);

<?php if (isset($_smarty_tpl->getVariable('pageTrack',null,true,false)->value)){?>_gaq.push(['_trackPageview', '<?php echo $_smarty_tpl->getVariable('pageTrack')->value;?>
']);<?php }?>

<?php if ($_smarty_tpl->getVariable('isOrder')->value==true){?>		
  _gaq.push(['_addTrans',
    '<?php echo $_smarty_tpl->getVariable('trans')->value['id'];?>
',			
    '<?php echo $_smarty_tpl->getVariable('trans')->value['store'];?>
',		
    '<?php echo $_smarty_tpl->getVariable('trans')->value['total'];?>
',		
    '<?php echo $_smarty_tpl->getVariable('trans')->value['tax'];?>
',			
    '<?php echo $_smarty_tpl->getVariable('trans')->value['shipping'];?>
',	
    '<?php echo $_smarty_tpl->getVariable('trans')->value['city'];?>
',		
    '<?php echo $_smarty_tpl->getVariable('trans')->value['state'];?>
',		
    '<?php echo $_smarty_tpl->getVariable('trans')->value['country'];?>
'		
  ]);

	<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('items')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
?>
		_gaq.push(['_addItem',
		'<?php echo $_smarty_tpl->tpl_vars['item']->value['OrderId'];?>
',		
		'<?php echo $_smarty_tpl->tpl_vars['item']->value['SKU'];?>
',			
		'<?php echo $_smarty_tpl->tpl_vars['item']->value['Product'];?>
',		
		'<?php echo $_smarty_tpl->tpl_vars['item']->value['Category'];?>
',		
		'<?php echo $_smarty_tpl->tpl_vars['item']->value['Price'];?>
',		
		'<?php echo $_smarty_tpl->tpl_vars['item']->value['Quantity'];?>
'		
		]);
	<?php }} ?>

  _gaq.push(['_trackTrans']);	

<?php }?>

(function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})(); 
</script>
