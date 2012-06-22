<?php /* Smarty version Smarty-3.0.7, created on 2012-06-15 17:35:41
         compiled from "/data/web/virtuals/24582/virtual/www/themes/prestashop/404.tpl" */ ?>
<?php /*%%SmartyHeaderCode:19055902434fdb564dc06681-21043535%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cda99c893b2f0c5f3d1b4ccec082b18de3b00288' => 
    array (
      0 => '/data/web/virtuals/24582/virtual/www/themes/prestashop/404.tpl',
      1 => 1339512194,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19055902434fdb564dc06681-21043535',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>


<h1><?php echo smartyTranslate(array('s'=>'Page not available'),$_smarty_tpl);?>
</h1>

<p class="error">
	<img src="<?php echo $_smarty_tpl->getVariable('img_dir')->value;?>
icon/error.gif" alt="<?php echo smartyTranslate(array('s'=>'Error'),$_smarty_tpl);?>
" class="middle" />
	<?php echo smartyTranslate(array('s'=>'We\'re sorry, but the Web address you entered is no longer available'),$_smarty_tpl);?>

</p>

<h3><?php echo smartyTranslate(array('s'=>'To find a product, please type its name in the field below'),$_smarty_tpl);?>
</h3>

<form action="<?php echo $_smarty_tpl->getVariable('link')->value->getPageLink('search.php');?>
" method="get" class="std">
	<fieldset>
		<p>
			<label for="search"><?php echo smartyTranslate(array('s'=>'Search our product catalog:'),$_smarty_tpl);?>
</label>
			<input id="search_query" class="page404_input" name="search_query" type="text" />
			<input type="submit" name="Submit" value="<?php echo smartyTranslate(array('s'=>'Search'),$_smarty_tpl);?>
" class="page404_input button_small" />
		</p>
	</fieldset>
	<div class="clear"></div>
</form>

<p><a href="<?php echo $_smarty_tpl->getVariable('base_dir')->value;?>
" title="<?php echo smartyTranslate(array('s'=>'Home'),$_smarty_tpl);?>
"><img src="<?php echo $_smarty_tpl->getVariable('img_dir')->value;?>
icon/home.gif" alt="<?php echo smartyTranslate(array('s'=>'Home'),$_smarty_tpl);?>
" class="icon" /></a><a href="<?php echo $_smarty_tpl->getVariable('base_dir')->value;?>
" title="<?php echo smartyTranslate(array('s'=>'Home'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Home'),$_smarty_tpl);?>
</a></p>
