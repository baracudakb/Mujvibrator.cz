<?php /* Smarty version Smarty-3.0.7, created on 2012-06-20 23:16:15
         compiled from "/data/web/virtuals/24582/virtual/www/themes/prestashop/./product-compare.tpl" */ ?>
<?php /*%%SmartyHeaderCode:18075075894fe23d9f8430e6-74036487%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '025bc87a407340ded7d62b4dd2b7a90931c20ce5' => 
    array (
      0 => '/data/web/virtuals/24582/virtual/www/themes/prestashop/./product-compare.tpl',
      1 => 1339512226,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18075075894fe23d9f8430e6-74036487',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>


<?php if ($_smarty_tpl->getVariable('comparator_max_item')->value){?>
<script type="text/javascript">
// <![CDATA[
	var min_item = '<?php echo smartyTranslate(array('s'=>'Please select at least one product.','js'=>1),$_smarty_tpl);?>
';
	var max_item = "<?php echo smartyTranslate(array('s'=>'You cannot add more than','js'=>1),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->getVariable('comparator_max_item')->value;?>
 <?php echo smartyTranslate(array('s'=>'product(s) in the product comparator','js'=>1),$_smarty_tpl);?>
";
//]]>
</script>
	<form method="get" action="<?php echo $_smarty_tpl->getVariable('link')->value->getPageLink('products-comparison.php');?>
" onsubmit="true">
		<p>
		<input type="submit" class="button" value="<?php echo smartyTranslate(array('s'=>'Compare'),$_smarty_tpl);?>
" style="float:right" />
		<input type="hidden" name="compare_product_list" class="compare_product_list" value="" />
		</p>
	</form>
<?php }?>

