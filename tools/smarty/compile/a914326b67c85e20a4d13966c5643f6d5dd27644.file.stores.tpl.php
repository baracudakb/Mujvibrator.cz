<?php /* Smarty version Smarty-3.0.7, created on 2012-06-17 06:08:20
         compiled from "/data/web/virtuals/24582/virtual/www/themes/prestashop/stores.tpl" */ ?>
<?php /*%%SmartyHeaderCode:19731414074fdd58340e8924-50015783%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a914326b67c85e20a4d13966c5643f6d5dd27644' => 
    array (
      0 => '/data/web/virtuals/24582/virtual/www/themes/prestashop/stores.tpl',
      1 => 1339512227,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19731414074fdd58340e8924-50015783',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_escape')) include '/data/web/virtuals/24582/virtual/www/tools/smarty/plugins/modifier.escape.php';
?>

<?php ob_start(); ?><?php echo smartyTranslate(array('s'=>'Our stores'),$_smarty_tpl);?>
<?php  Smarty::$_smarty_vars['capture']['path']=ob_get_clean();?>
<?php $_template = new Smarty_Internal_Template(($_smarty_tpl->getVariable('tpl_dir')->value)."./breadcrumb.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>

<script src="http://maps.google.com/maps/api/js?sensor=true">// DEBUG </script>

<h1><?php echo smartyTranslate(array('s'=>'Our stores'),$_smarty_tpl);?>
</h1>

<?php if ($_smarty_tpl->getVariable('simplifiedStoresDiplay')->value){?>
	<?php if (count($_smarty_tpl->getVariable('stores')->value)){?>
	<p><?php echo smartyTranslate(array('s'=>'Here are the details of our stores, feel free to contact us:'),$_smarty_tpl);?>
</p>
	<?php  $_smarty_tpl->tpl_vars['store'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('stores')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['store']->key => $_smarty_tpl->tpl_vars['store']->value){
?>
		<div class="store-small">
			<?php if ($_smarty_tpl->tpl_vars['store']->value['has_picture']){?><p><img src="<?php echo $_smarty_tpl->getVariable('img_store_dir')->value;?>
<?php echo $_smarty_tpl->tpl_vars['store']->value['id_store'];?>
-medium.jpg" alt="" width="<?php echo $_smarty_tpl->getVariable('mediumSize')->value['width'];?>
" height="<?php echo $_smarty_tpl->getVariable('mediumSize')->value['height'];?>
" /></p><?php }?>
			<p>
				<b><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['store']->value['name'],'htmlall','UTF-8');?>
</b><br />
				<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['store']->value['address1'],'htmlall','UTF-8');?>
<br />
				<?php if ($_smarty_tpl->tpl_vars['store']->value['address2']){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['store']->value['address2'],'htmlall','UTF-8');?>
<?php }?><br />
				<?php echo $_smarty_tpl->tpl_vars['store']->value['postcode'];?>
 <?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['store']->value['city'],'htmlall','UTF-8');?>
<?php if ($_smarty_tpl->tpl_vars['store']->value['state']){?>, <?php echo $_smarty_tpl->tpl_vars['store']->value['state'];?>
<?php }?><br />
				<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['store']->value['country'],'htmlall','UTF-8');?>
<br />
				<?php if ($_smarty_tpl->tpl_vars['store']->value['phone']){?><?php echo smartyTranslate(array('s'=>'Phone:','js'=>0),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['store']->value['phone'];?>
<?php }?>
			</p>
		</div>
	<?php }} ?>
	<?php }?>
<?php }else{ ?>
	<script type="text/javascript">
		// <![CDATA[
		var map;
		var markers = [];
		var infoWindow;
		var locationSelect;

		var defaultLat = '<?php echo $_smarty_tpl->getVariable('defaultLat')->value;?>
';
		var defaultLong = '<?php echo $_smarty_tpl->getVariable('defaultLong')->value;?>
';
		
		var translation_1 = '<?php echo smartyTranslate(array('s'=>'No store found, try to select a wider radius','js'=>1),$_smarty_tpl);?>
';
		var translation_2 = '<?php echo smartyTranslate(array('s'=>'store found - see details:','js'=>1),$_smarty_tpl);?>
';
		var translation_3 = '<?php echo smartyTranslate(array('s'=>'stores found - see all results:','js'=>1),$_smarty_tpl);?>
';
		var translation_4 = '<?php echo smartyTranslate(array('s'=>'Phone:','js'=>1),$_smarty_tpl);?>
';
		var translation_5 = '<?php echo smartyTranslate(array('s'=>'Get Directions','js'=>1),$_smarty_tpl);?>
';
		var translation_6 = '<?php echo smartyTranslate(array('s'=>'Not found','js'=>1),$_smarty_tpl);?>
';
		
		var hasStoreIcon = '<?php echo $_smarty_tpl->getVariable('hasStoreIcon')->value;?>
';
		var distance_unit = '<?php echo $_smarty_tpl->getVariable('distance_unit')->value;?>
';
		var img_store_dir = '<?php echo $_smarty_tpl->getVariable('img_store_dir')->value;?>
';
		var img_ps_dir = '<?php echo $_smarty_tpl->getVariable('img_ps_dir')->value;?>
';
		var searchUrl = '<?php echo $_smarty_tpl->getVariable('searchUrl')->value;?>
';
		//]]>
	</script>

	<p><?php echo smartyTranslate(array('s'=>'Enter a location (eg.: a zip/postal code, an address, a city or a country) in order to find the nearest stores.'),$_smarty_tpl);?>
</p>
	<p><?php echo smartyTranslate(array('s'=>'Your location:'),$_smarty_tpl);?>
 <input type="text" name="location" id="addressInput" value="<?php echo smartyTranslate(array('s'=>'Address, zip/postal code, city, state or country'),$_smarty_tpl);?>
" onclick="this.value='';" /></p>
	<p style="margin-top: 15px;">
		<?php echo smartyTranslate(array('s'=>'Radius:'),$_smarty_tpl);?>
 
		<select name="radius" id="radiusSelect">
			<option value="15">15</option>
			<option value="25">25</option>
			<option value="50">50</option>
			<option value="100">100</option>
		</select> <?php echo $_smarty_tpl->getVariable('distance_unit')->value;?>

		<input type="button" class="button" onclick="searchLocations();" value="<?php echo smartyTranslate(array('s'=>'Search'),$_smarty_tpl);?>
" style="display: inline;" /> 
		<img src="<?php echo $_smarty_tpl->getVariable('img_ps_dir')->value;?>
loader.gif" class="middle" alt="" id="stores_loader" />
	</p>
	<div><select id="locationSelect"></select></div>
    <div id="map"></div>
	<table cellpadding="0" cellspacing="0" id="stores-table">
		<tr>
			<th><?php echo smartyTranslate(array('s'=>'#'),$_smarty_tpl);?>
</th>
			<th><?php echo smartyTranslate(array('s'=>'Store'),$_smarty_tpl);?>
</th>
			<th><?php echo smartyTranslate(array('s'=>'Address'),$_smarty_tpl);?>
</th>
			<th><?php echo smartyTranslate(array('s'=>'Distance'),$_smarty_tpl);?>
</th>
		</tr>		
	</table>
<?php }?>
