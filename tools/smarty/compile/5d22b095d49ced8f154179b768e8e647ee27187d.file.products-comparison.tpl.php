<?php /* Smarty version Smarty-3.0.7, created on 2012-06-12 22:36:34
         compiled from "/data/web/virtuals/24582/virtual/www/themes/prestashop/products-comparison.tpl" */ ?>
<?php /*%%SmartyHeaderCode:18618072434fd7a852d82117-66535156%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5d22b095d49ced8f154179b768e8e647ee27187d' => 
    array (
      0 => '/data/web/virtuals/24582/virtual/www/themes/prestashop/products-comparison.tpl',
      1 => 1339512227,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18618072434fd7a852d82117-66535156',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_escape')) include '/data/web/virtuals/24582/virtual/www/tools/smarty/plugins/modifier.escape.php';
if (!is_callable('smarty_function_math')) include '/data/web/virtuals/24582/virtual/www/tools/smarty/plugins/function.math.php';
if (!is_callable('smarty_function_cycle')) include '/data/web/virtuals/24582/virtual/www/tools/smarty/plugins/function.cycle.php';
?>

<?php ob_start(); ?><?php echo smartyTranslate(array('s'=>'Product Comparison'),$_smarty_tpl);?>
<?php  Smarty::$_smarty_vars['capture']['path']=ob_get_clean();?>

<?php $_template = new Smarty_Internal_Template(($_smarty_tpl->getVariable('tpl_dir')->value)."./breadcrumb.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<h1><?php echo smartyTranslate(array('s'=>'Product Comparison'),$_smarty_tpl);?>
</h1>

<?php if ($_smarty_tpl->getVariable('hasProduct')->value){?>
<div class="products_block">
	<table id="product_comparison">
			<td width="20%"></td>
			<?php $_smarty_tpl->tpl_vars['taxes_behavior'] = new Smarty_variable(false, null, null);?>
			<?php if ($_smarty_tpl->getVariable('use_taxes')->value&&(!$_smarty_tpl->getVariable('priceDisplay')->value||$_smarty_tpl->getVariable('priceDisplay')->value==2)){?>
				<?php $_smarty_tpl->tpl_vars['taxes_behavior'] = new Smarty_variable(true, null, null);?>
			<?php }?>
		<?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('products')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value){
?>
			<?php $_smarty_tpl->tpl_vars['replace_id'] = new Smarty_variable(($_smarty_tpl->getVariable('product')->value->id).('|'), null, null);?>

			<td width="<?php echo $_smarty_tpl->getVariable('width')->value;?>
%" class="ajax_block_product comparison_infos">
				<h5><a href="<?php echo $_smarty_tpl->getVariable('product')->value->getLink();?>
" title="<?php echo smarty_modifier_escape(smarty_modifier_truncate($_smarty_tpl->getVariable('product')->value->name,32,'...'),'htmlall','UTF-8');?>
"><?php echo smarty_modifier_escape(smarty_modifier_truncate($_smarty_tpl->getVariable('product')->value->name,27,'...'),'htmlall','UTF-8');?>
</a></h5>
				<div class="product_desc"><a href="<?php echo $_smarty_tpl->getVariable('product')->value->getLink();?>
" title="<?php echo smartyTranslate(array('s'=>'More'),$_smarty_tpl);?>
"><?php echo smarty_modifier_truncate(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->getVariable('product')->value->description_short),130,'...');?>
</a></div>
				<div class="comparison_product_infos">
					<a href="<?php echo $_smarty_tpl->getVariable('product')->value->getLink();?>
" title="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('product')->value->name,'html','UTF-8');?>
" class="product_image" >
						<img src="<?php echo $_smarty_tpl->getVariable('link')->value->getImageLink($_smarty_tpl->getVariable('product')->value->link_rewrite,$_smarty_tpl->getVariable('product')->value->id_image,'home');?>
" alt="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('product')->value->name,'html','UTF-8');?>
" width="<?php echo $_smarty_tpl->getVariable('homeSize')->value['width'];?>
" height="<?php echo $_smarty_tpl->getVariable('homeSize')->value['height'];?>
" />
					</a>
				<?php if (isset($_smarty_tpl->getVariable('product',null,true,false)->value->show_price)&&$_smarty_tpl->getVariable('product')->value->show_price&&!isset($_smarty_tpl->getVariable('restricted_country_mode',null,true,false)->value)&&!$_smarty_tpl->getVariable('PS_CATALOG_MODE')->value){?>
					<p class="price_container"><span class="price"><?php echo Product::convertPrice(array('price'=>$_smarty_tpl->getVariable('product')->value->getPrice($_smarty_tpl->getVariable('taxes_behavior')->value)),$_smarty_tpl);?>
</span></p>
					<div class="product_discount">
					<?php if ($_smarty_tpl->getVariable('product')->value->on_sale){?>
						<span class="on_sale"><?php echo smartyTranslate(array('s'=>'On sale!'),$_smarty_tpl);?>
</span>
					<?php }elseif($_smarty_tpl->getVariable('product')->value->specificPrice&&$_smarty_tpl->getVariable('product')->value->specificPrice['reduction']){?>
						<span class="discount"><?php echo smartyTranslate(array('s'=>'Reduced price!'),$_smarty_tpl);?>
</span>
					<?php }?>
					</div>

					<?php if (!empty($_smarty_tpl->getVariable('product',null,true,false)->value->unity)&&$_smarty_tpl->getVariable('product')->value->unit_price_ratio>0.000000){?>
						    <?php echo smarty_function_math(array('equation'=>"pprice / punit_price",'pprice'=>$_smarty_tpl->getVariable('product')->value->getPrice($_smarty_tpl->getVariable('taxes_behavior')->value),'punit_price'=>$_smarty_tpl->getVariable('product')->value->unit_price_ratio,'assign'=>'unit_price'),$_smarty_tpl);?>

						<p class="comparison_unit_price"><?php echo Product::convertPrice(array('price'=>$_smarty_tpl->getVariable('unit_price')->value),$_smarty_tpl);?>
 <?php echo smartyTranslate(array('s'=>'per'),$_smarty_tpl);?>
 <?php echo smarty_modifier_escape($_smarty_tpl->getVariable('product')->value->unity,'htmlall','UTF-8');?>
</p>
					<?php }else{ ?>
					&nbsp;
					<?php }?>
				<?php }?>
				<!-- availability -->
				<p class="comparison_availability_statut">
					<?php if (!(($_smarty_tpl->getVariable('product')->value->quantity<=0&&!$_smarty_tpl->getVariable('product')->value->available_later)||($_smarty_tpl->getVariable('product')->value->quantity!=0&&!$_smarty_tpl->getVariable('product')->value->available_now)||!$_smarty_tpl->getVariable('product')->value->available_for_order||$_smarty_tpl->getVariable('PS_CATALOG_MODE')->value)){?>
						<span id="availability_label"><?php echo smartyTranslate(array('s'=>'Availability:'),$_smarty_tpl);?>
</span>
						<span id="availability_value"<?php if ($_smarty_tpl->getVariable('product')->value->quantity<=0){?> class="warning-inline"<?php }?>>
							<?php if ($_smarty_tpl->getVariable('product')->value->quantity<=0){?>
								<?php if ($_smarty_tpl->getVariable('allow_oosp')->value){?>
									<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('product')->value->available_later,'htmlall','UTF-8');?>

								<?php }else{ ?>
									<?php echo smartyTranslate(array('s'=>'This product is no longer in stock'),$_smarty_tpl);?>

								<?php }?>
							<?php }else{ ?>
								<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('product')->value->available_now,'htmlall','UTF-8');?>

							<?php }?>
						</span>
					<?php }?>
				</p>
					<a class="cmp_remove" href="<?php echo $_smarty_tpl->getVariable('link')->value->getPageLink('products-comparison.php');?>
" rel="ajax_id_product_<?php echo $_smarty_tpl->getVariable('product')->value->id;?>
"><?php echo smartyTranslate(array('s'=>'Remove'),$_smarty_tpl);?>
</a>
					<a class="button" href="<?php echo $_smarty_tpl->getVariable('product')->value->getLink();?>
" title="<?php echo smartyTranslate(array('s'=>'View'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'View'),$_smarty_tpl);?>
</a>
					<?php if ((!$_smarty_tpl->getVariable('product')->value->hasAttributes()||(isset($_smarty_tpl->getVariable('add_prod_display',null,true,false)->value)&&($_smarty_tpl->getVariable('add_prod_display')->value==1)))&&$_smarty_tpl->getVariable('product')->value->minimal_quantity==1&&$_smarty_tpl->getVariable('product')->value->customizable!=2&&!$_smarty_tpl->getVariable('PS_CATALOG_MODE')->value){?>
						<?php if (($_smarty_tpl->getVariable('product')->value->quantity>0||$_smarty_tpl->getVariable('product')->value->allow_oosp)){?>
							<a class="exclusive ajax_add_to_cart_button" rel="ajax_id_product_<?php echo $_smarty_tpl->getVariable('product')->value->id;?>
" href="<?php echo $_smarty_tpl->getVariable('link')->value->getPageLink('cart.php');?>
?qty=1&amp;id_product=<?php echo $_smarty_tpl->getVariable('product')->value->id;?>
&amp;token=<?php echo $_smarty_tpl->getVariable('static_token')->value;?>
&amp;add" title="<?php echo smartyTranslate(array('s'=>'Add to cart'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Add to cart'),$_smarty_tpl);?>
</a>
						<?php }else{ ?>
							<span class="exclusive"><?php echo smartyTranslate(array('s'=>'Add to cart'),$_smarty_tpl);?>
</span>
						<?php }?>
					<?php }else{ ?>
						<div style="height:23px;"></div>
					<?php }?>
				</div>
			</td>
		<?php }} ?>
		</tr>

		<tr class="comparison_header">
			<td>
				<?php echo smartyTranslate(array('s'=>'Features'),$_smarty_tpl);?>

			</td>
			<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['td']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['td']['loop'] = is_array($_loop=count($_smarty_tpl->getVariable('products')->value)) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['td']['step'] = ((int)1) == 0 ? 1 : (int)1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['td']['start'] = (int)0;
$_smarty_tpl->tpl_vars['smarty']->value['section']['td']['name'] = 'td';
$_smarty_tpl->tpl_vars['smarty']->value['section']['td']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['td']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['loop'];
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['td']['start'] < 0)
    $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['start'] = max($_smarty_tpl->tpl_vars['smarty']->value['section']['td']['step'] > 0 ? 0 : -1, $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['loop'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['start']);
else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['start'] = min($_smarty_tpl->tpl_vars['smarty']->value['section']['td']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['step'] > 0 ? $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['loop'] : $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['loop']-1);
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['td']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['total'] = min(ceil(($_smarty_tpl->tpl_vars['smarty']->value['section']['td']['step'] > 0 ? $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['loop'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['start'] : $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['start']+1)/abs($_smarty_tpl->tpl_vars['smarty']->value['section']['td']['step'])), $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['max']);
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['td']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['td']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['td']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['td']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['td']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['td']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['td']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['td']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['td']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['td']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['td']['total']);
?>
			<td></td>
			<?php endfor; endif; ?>
		</tr>

		<?php if ($_smarty_tpl->getVariable('ordered_features')->value){?>
		<?php  $_smarty_tpl->tpl_vars['feature'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('ordered_features')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['feature']->key => $_smarty_tpl->tpl_vars['feature']->value){
?>
		<tr>
			<?php echo smarty_function_cycle(array('values'=>'comparison_feature_odd,comparison_feature_even','assign'=>'classname'),$_smarty_tpl);?>

			<td class="<?php echo $_smarty_tpl->getVariable('classname')->value;?>
" >
				<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['feature']->value['name'],'htmlall','UTF-8');?>

			</td>

				<?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('products')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value){
?>
					<?php $_smarty_tpl->tpl_vars['product_id'] = new Smarty_variable($_smarty_tpl->getVariable('product')->value->id, null, null);?>
					<?php $_smarty_tpl->tpl_vars['feature_id'] = new Smarty_variable($_smarty_tpl->tpl_vars['feature']->value['id_feature'], null, null);?>
					<?php if (isset($_smarty_tpl->getVariable('product_features',null,true,false)->value[$_smarty_tpl->getVariable('product_id',null,true,false)->value])){?>
						<?php $_smarty_tpl->tpl_vars['tab'] = new Smarty_variable($_smarty_tpl->getVariable('product_features')->value[$_smarty_tpl->getVariable('product_id')->value], null, null);?>
						<td  width="<?php echo $_smarty_tpl->getVariable('width')->value;?>
%" class="<?php echo $_smarty_tpl->getVariable('classname')->value;?>
 comparison_infos"><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('tab')->value[$_smarty_tpl->getVariable('feature_id')->value],'htmlall','UTF-8');?>
</td>
					<?php }else{ ?>
						<td  width="<?php echo $_smarty_tpl->getVariable('width')->value;?>
%" class="<?php echo $_smarty_tpl->getVariable('classname')->value;?>
 comparison_infos"></td>
					<?php }?>
				<?php }} ?>
		</tr>
		<?php }} ?>
		<?php }else{ ?>
			<tr>
				<td></td>
				<td colspan="<?php echo count($_smarty_tpl->getVariable('products')->value)+1;?>
"><?php echo smartyTranslate(array('s'=>'No features to compare'),$_smarty_tpl);?>
</td>
			</tr>
		<?php }?>

		<?php echo $_smarty_tpl->getVariable('HOOK_EXTRA_PRODUCT_COMPARISON')->value;?>

	</table>
</div>
<?php }else{ ?>
	<p class="warning"><?php echo smartyTranslate(array('s'=>'There is no product in the comparator'),$_smarty_tpl);?>
</p>
<?php }?>

