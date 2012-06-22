<?php /* Smarty version Smarty-3.0.7, created on 2012-06-18 16:05:29
         compiled from "/data/web/virtuals/24582/virtual/www/themes/prestashop/store_infos.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15561045894fdf35a9373631-65026130%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '11eb30001175a2eaa7a3dba8739147770763d8b4' => 
    array (
      0 => '/data/web/virtuals/24582/virtual/www/themes/prestashop/store_infos.tpl',
      1 => 1339512227,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15561045894fdf35a9373631-65026130',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>

<br />
<br />
<span id="store_hours"><?php echo smartyTranslate(array('s'=>'Hours:'),$_smarty_tpl);?>
</span>
<table style="font-size: 9px;">
	<?php  $_smarty_tpl->tpl_vars['one_day'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('days_datas')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['one_day']->key => $_smarty_tpl->tpl_vars['one_day']->value){
?>
	<tr>
		<td style="width: 70px;"><?php echo smartyTranslate(array('s'=>$_smarty_tpl->tpl_vars['one_day']->value['day']),$_smarty_tpl);?>
</td><td><?php echo $_smarty_tpl->tpl_vars['one_day']->value['hours'];?>
</td>
	</tr>
	<?php }} ?>
</table>
