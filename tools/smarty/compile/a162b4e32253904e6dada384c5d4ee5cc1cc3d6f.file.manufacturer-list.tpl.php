<?php /* Smarty version Smarty-3.0.7, created on 2012-06-17 01:34:58
         compiled from "/data/web/virtuals/24582/virtual/www/themes/prestashop/manufacturer-list.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5721018214fdd18228383b7-23666091%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a162b4e32253904e6dada384c5d4ee5cc1cc3d6f' => 
    array (
      0 => '/data/web/virtuals/24582/virtual/www/themes/prestashop/manufacturer-list.tpl',
      1 => 1339512224,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5721018214fdd18228383b7-23666091',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_escape')) include '/data/web/virtuals/24582/virtual/www/tools/smarty/plugins/modifier.escape.php';
?>

<?php ob_start(); ?><?php echo smartyTranslate(array('s'=>'Manufacturers'),$_smarty_tpl);?>
<?php  Smarty::$_smarty_vars['capture']['path']=ob_get_clean();?>
<?php $_template = new Smarty_Internal_Template(($_smarty_tpl->getVariable('tpl_dir')->value)."./breadcrumb.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>

<h1><?php echo smartyTranslate(array('s'=>'Manufacturers'),$_smarty_tpl);?>
</h1>

<?php if (isset($_smarty_tpl->getVariable('errors',null,true,false)->value)&&$_smarty_tpl->getVariable('errors')->value){?>
	<?php $_template = new Smarty_Internal_Template(($_smarty_tpl->getVariable('tpl_dir')->value)."./errors.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<?php }else{ ?>
	<p><span class="bold"><?php if ($_smarty_tpl->getVariable('nbManufacturers')->value==0){?><?php echo smartyTranslate(array('s'=>'There are no manufacturers.'),$_smarty_tpl);?>
<?php }else{ ?><?php if ($_smarty_tpl->getVariable('nbManufacturers')->value==1){?><?php echo smartyTranslate(array('s'=>'There is'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'There are'),$_smarty_tpl);?>
<?php }?>&#160;<?php echo $_smarty_tpl->getVariable('nbManufacturers')->value;?>
&#160;<?php if ($_smarty_tpl->getVariable('nbManufacturers')->value==1){?><?php echo smartyTranslate(array('s'=>'manufacturer.'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'manufacturers.'),$_smarty_tpl);?>
<?php }?><?php }?></span>
	</p>

	<?php if ($_smarty_tpl->getVariable('nbManufacturers')->value>0){?>
		<ul id="manufacturers_list">
		<?php  $_smarty_tpl->tpl_vars['manufacturer'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('manufacturers')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['manufacturer']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['manufacturer']->iteration=0;
 $_smarty_tpl->tpl_vars['manufacturer']->index=-1;
if ($_smarty_tpl->tpl_vars['manufacturer']->total > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['manufacturer']->key => $_smarty_tpl->tpl_vars['manufacturer']->value){
 $_smarty_tpl->tpl_vars['manufacturer']->iteration++;
 $_smarty_tpl->tpl_vars['manufacturer']->index++;
 $_smarty_tpl->tpl_vars['manufacturer']->first = $_smarty_tpl->tpl_vars['manufacturer']->index === 0;
 $_smarty_tpl->tpl_vars['manufacturer']->last = $_smarty_tpl->tpl_vars['manufacturer']->iteration === $_smarty_tpl->tpl_vars['manufacturer']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['manufacturers']['first'] = $_smarty_tpl->tpl_vars['manufacturer']->first;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['manufacturers']['last'] = $_smarty_tpl->tpl_vars['manufacturer']->last;
?>
			<li class="clearfix <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['manufacturers']['first']){?>first_item<?php }elseif($_smarty_tpl->getVariable('smarty')->value['foreach']['manufacturers']['last']){?>last_item<?php }else{ ?>item<?php }?>"> 
				<div class="left_side">
					<!-- logo -->
					<div class="logo">
					<?php if ($_smarty_tpl->tpl_vars['manufacturer']->value['nb_products']>0){?><a href="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('link')->value->getmanufacturerLink($_smarty_tpl->tpl_vars['manufacturer']->value['id_manufacturer'],$_smarty_tpl->tpl_vars['manufacturer']->value['link_rewrite']),'htmlall','UTF-8');?>
" title="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['manufacturer']->value['name'],'htmlall','UTF-8');?>
"><?php }?>
						<img src="<?php echo $_smarty_tpl->getVariable('img_manu_dir')->value;?>
<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['manufacturer']->value['image'],'htmlall','UTF-8');?>
-medium.jpg" alt="" width="<?php echo $_smarty_tpl->getVariable('mediumSize')->value['width'];?>
" height="<?php echo $_smarty_tpl->getVariable('mediumSize')->value['height'];?>
" />
					<?php if ($_smarty_tpl->tpl_vars['manufacturer']->value['nb_products']>0){?></a><?php }?>
					</div>
					<!-- name -->
					<h3>
						<?php if ($_smarty_tpl->tpl_vars['manufacturer']->value['nb_products']>0){?><a href="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('link')->value->getmanufacturerLink($_smarty_tpl->tpl_vars['manufacturer']->value['id_manufacturer'],$_smarty_tpl->tpl_vars['manufacturer']->value['link_rewrite']),'htmlall','UTF-8');?>
"><?php }?>
						<?php echo smarty_modifier_escape(smarty_modifier_truncate($_smarty_tpl->tpl_vars['manufacturer']->value['name'],60,'...'),'htmlall','UTF-8');?>

						<?php if ($_smarty_tpl->tpl_vars['manufacturer']->value['nb_products']>0){?></a><?php }?>
					</h3>
					<p class="description rte">
					<?php if ($_smarty_tpl->tpl_vars['manufacturer']->value['nb_products']>0){?><a href="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('link')->value->getmanufacturerLink($_smarty_tpl->tpl_vars['manufacturer']->value['id_manufacturer'],$_smarty_tpl->tpl_vars['manufacturer']->value['link_rewrite']),'htmlall','UTF-8');?>
"><?php }?>
					<?php echo $_smarty_tpl->tpl_vars['manufacturer']->value['description'];?>

					<?php if ($_smarty_tpl->tpl_vars['manufacturer']->value['nb_products']>0){?></a><?php }?>
					</p>
				</div>

				<div class="right_side">
				<?php if ($_smarty_tpl->tpl_vars['manufacturer']->value['nb_products']>0){?><a href="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('link')->value->getmanufacturerLink($_smarty_tpl->tpl_vars['manufacturer']->value['id_manufacturer'],$_smarty_tpl->tpl_vars['manufacturer']->value['link_rewrite']),'htmlall','UTF-8');?>
"><?php }?>
					<span><?php echo intval($_smarty_tpl->tpl_vars['manufacturer']->value['nb_products']);?>
 <?php if ($_smarty_tpl->tpl_vars['manufacturer']->value['nb_products']==1){?><?php echo smartyTranslate(array('s'=>'product'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'products'),$_smarty_tpl);?>
<?php }?></span>
				<?php if ($_smarty_tpl->tpl_vars['manufacturer']->value['nb_products']>0){?></a><?php }?>

				<?php if ($_smarty_tpl->tpl_vars['manufacturer']->value['nb_products']>0){?>
					<a class="button" href="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('link')->value->getmanufacturerLink($_smarty_tpl->tpl_vars['manufacturer']->value['id_manufacturer'],$_smarty_tpl->tpl_vars['manufacturer']->value['link_rewrite']),'htmlall','UTF-8');?>
"><?php echo smartyTranslate(array('s'=>'view products'),$_smarty_tpl);?>
</a>
				<?php }?>
				</div>
				<br class="clear"/>
			</li>
		<?php }} ?>
		</ul>
		<?php $_template = new Smarty_Internal_Template(($_smarty_tpl->getVariable('tpl_dir')->value)."./pagination.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
	<?php }?>
<?php }?>
