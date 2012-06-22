<?php /* Smarty version Smarty-3.0.7, created on 2012-06-12 22:51:07
         compiled from "/data/web/virtuals/24582/virtual/www/themes/prestashop/category-count.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15199533974fd7abbb7f07b1-46100849%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c3bcdfb0e7d6a7c8752c7a23a3306280efdacb6f' => 
    array (
      0 => '/data/web/virtuals/24582/virtual/www/themes/prestashop/category-count.tpl',
      1 => 1339512195,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15199533974fd7abbb7f07b1-46100849',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>

<?php if ($_smarty_tpl->getVariable('category')->value->id==1||$_smarty_tpl->getVariable('nb_products')->value==0){?><?php echo smartyTranslate(array('s'=>'There are no products.'),$_smarty_tpl);?>

<?php }else{ ?>
	<?php if ($_smarty_tpl->getVariable('nb_products')->value==1){?><?php echo smartyTranslate(array('s'=>'There is'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'There are'),$_smarty_tpl);?>
<?php }?>
	<?php echo $_smarty_tpl->getVariable('nb_products')->value;?>

	<?php if ($_smarty_tpl->getVariable('nb_products')->value==1){?><?php echo smartyTranslate(array('s'=>'product.'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'products.'),$_smarty_tpl);?>
<?php }?>
<?php }?>