<?php /* Smarty version Smarty-3.0.7, created on 2012-06-20 23:16:59
         compiled from "/data/web/virtuals/24582/virtual/www/themes/prestashop/footer.tpl" */ ?>
<?php /*%%SmartyHeaderCode:18536918694fe23dcb1e44b9-29725481%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a5aa47b5719947b59f135ea1683fb390e86ef850' => 
    array (
      0 => '/data/web/virtuals/24582/virtual/www/themes/prestashop/footer.tpl',
      1 => 1339512204,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18536918694fe23dcb1e44b9-29725481',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>


		<?php if (!$_smarty_tpl->getVariable('content_only')->value){?>
				</div>

<!-- Right -->
				<div id="right_column" class="column">
					<?php echo $_smarty_tpl->getVariable('HOOK_RIGHT_COLUMN')->value;?>

				</div>
			</div>

<!-- Footer -->
			<div id="footer"><?php echo $_smarty_tpl->getVariable('HOOK_FOOTER')->value;?>
</div>
		</div>
	<?php }?>
	</body>
</html>
