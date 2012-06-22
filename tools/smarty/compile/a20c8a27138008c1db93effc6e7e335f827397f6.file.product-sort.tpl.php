<?php /* Smarty version Smarty-3.0.7, created on 2012-06-20 23:16:15
         compiled from "/data/web/virtuals/24582/virtual/www/themes/prestashop/./product-sort.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15251479794fe23d9f3d7ce3-11815975%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a20c8a27138008c1db93effc6e7e335f827397f6' => 
    array (
      0 => '/data/web/virtuals/24582/virtual/www/themes/prestashop/./product-sort.tpl',
      1 => 1339512226,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15251479794fe23d9f3d7ce3-11815975',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_escape')) include '/data/web/virtuals/24582/virtual/www/tools/smarty/plugins/modifier.escape.php';
?>

<?php if (isset($_smarty_tpl->getVariable('orderby',null,true,false)->value)&&isset($_smarty_tpl->getVariable('orderway',null,true,false)->value)){?>
<!-- Sort products -->
<?php if (isset($_GET['id_category'])&&$_GET['id_category']){?>
	<?php $_smarty_tpl->tpl_vars['request'] = new Smarty_variable($_smarty_tpl->getVariable('link')->value->getPaginationLink('category',$_smarty_tpl->getVariable('category')->value,false,true), null, null);?>
<?php }elseif(isset($_GET['id_manufacturer'])&&$_GET['id_manufacturer']){?>
	<?php $_smarty_tpl->tpl_vars['request'] = new Smarty_variable($_smarty_tpl->getVariable('link')->value->getPaginationLink('manufacturer',$_smarty_tpl->getVariable('manufacturer')->value,false,true), null, null);?>
<?php }elseif(isset($_GET['id_supplier'])&&$_GET['id_supplier']){?>
	<?php $_smarty_tpl->tpl_vars['request'] = new Smarty_variable($_smarty_tpl->getVariable('link')->value->getPaginationLink('supplier',$_smarty_tpl->getVariable('supplier')->value,false,true), null, null);?>
<?php }else{ ?>
	<?php $_smarty_tpl->tpl_vars['request'] = new Smarty_variable($_smarty_tpl->getVariable('link')->value->getPaginationLink(false,false,false,true), null, null);?>
<?php }?>

<script type="text/javascript">
//<![CDATA[

$(document).ready(function()
{
	$('#selectPrductSort').change(function()
	{
		var requestSortProducts = '<?php echo $_smarty_tpl->getVariable('request')->value;?>
';
		var splitData = $(this).val().split(':');
		document.location.href = requestSortProducts + ((requestSortProducts.indexOf('?') < 0) ? '?' : '&') + 'orderby=' + splitData[0] + '&orderway=' + splitData[1];
	});
});
//]]>

</script>

<form id="productsSortForm" action="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('request')->value,'htmlall','UTF-8');?>
">
	<p class="select">
		<select id="selectPrductSort">
			<option value="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('orderbydefault')->value,'htmlall','UTF-8');?>
:<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('orderwaydefault')->value,'htmlall','UTF-8');?>
" <?php if ($_smarty_tpl->getVariable('orderby')->value==$_smarty_tpl->getVariable('orderbydefault')->value){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'--'),$_smarty_tpl);?>
</option>
			<?php if (!$_smarty_tpl->getVariable('PS_CATALOG_MODE')->value){?>
				<option value="price:asc" <?php if ($_smarty_tpl->getVariable('orderby')->value=='price'&&$_smarty_tpl->getVariable('orderway')->value=='asc'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Price: lowest first'),$_smarty_tpl);?>
</option>
				<option value="price:desc" <?php if ($_smarty_tpl->getVariable('orderby')->value=='price'&&$_smarty_tpl->getVariable('orderway')->value=='desc'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Price: highest first'),$_smarty_tpl);?>
</option>
			<?php }?>
			<option value="name:asc" <?php if ($_smarty_tpl->getVariable('orderby')->value=='name'&&$_smarty_tpl->getVariable('orderway')->value=='asc'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Product Name: A to Z'),$_smarty_tpl);?>
</option>
			<option value="name:desc" <?php if ($_smarty_tpl->getVariable('orderby')->value=='name'&&$_smarty_tpl->getVariable('orderway')->value=='desc'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Product Name: Z to A'),$_smarty_tpl);?>
</option>
			<?php if (!$_smarty_tpl->getVariable('PS_CATALOG_MODE')->value){?>
				<option value="quantity:desc" <?php if ($_smarty_tpl->getVariable('orderby')->value=='quantity'&&$_smarty_tpl->getVariable('orderway')->value=='desc'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'In-stock first'),$_smarty_tpl);?>
</option>
			<?php }?>
		</select>
		<label for="selectPrductSort"><?php echo smartyTranslate(array('s'=>'Sort by'),$_smarty_tpl);?>
</label>
	</p>
</form>
<!-- /Sort products -->
<?php }?>
