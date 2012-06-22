<script type="text/javascript" src="../modules/{$glob.module_name}/js/service.js"></script>
<p>{l s='You can bill to your customers additionnal fees from the cost charged by TNT, according to the type of service, the region of France(ex: Corse) or the weight of the package' mod='tntcarrier'}</p><br/>
<a href="index.php?tab={$glob.tab}&configure={$glob.configure}&token={$glob.token}&tab_module={$glob.tab_module}&module_name={$glob.module_name}&id_tab=3&section=service&action=new">
<img src="../img/admin/add.gif" alt="add"/> {l s='Add a TNT service via its specific code' mod='tntcarrier'}</a> <a target="_BLANK" href="../modules/{$glob.module_name}/tntDocumentation.pdf">{l s='(cf. Configuration guide attached)' mod='tntcarrier'}</a></br><br/>
<table class="table" cellspacing="0" cellpading="0">
	<tr>
		<!--<th>{l s='Id' mod='tntcarrier'}</th>-->
		<th>{l s='Name' mod='tntcarrier'}</th>
		<th>{l s='Description' mod='tntcarrier'}</th>
		<!--<th>{l s='code' mod='tntcarrier'}</th>-->
		<th>{l s='Additionnal Charge(Euros)' mod='tntcarrier'}</th>
		<th>{l s='Activated' mod='tntcarrier'}</th>
		<th></th>
	</tr>
{foreach from=$varService.serviceList  key=k item=v}
	<tr '.($irow++ % 2 ? 'class="alt_row"' : '').'>
		<!--<td>{$v.optionId}</td>-->
		<td>{$v.name}</td>
		<td>{$v.delay}</td>
		<!--<td>{$v.option}</td>-->
		<td>{$v.optionOvercost}</td>
		<td>
			{if $v.deleted != 1}
			<img style="cursor:pointer" onclick="changeActive(this,'{$v.optionId}', '{$glob.tnt_token}')" src="../img/admin/enabled.gif" />
			{else}
			<img style="cursor:pointer" onclick="changeActive(this,'{$v.optionId}', '{$glob.tnt_token}')" src="../img/admin/disabled.gif" />
			{/if}
		</td>
		<td>
			<a href="index.php?tab={$glob.tab}&configure={$glob.configure}&token={$glob.token}&tab_module={$glob.tab_module}&module_name={$glob.module_name}&id_tab=3&section=service&action=edit&service={$v.optionId}">
				<img src="../img/admin/edit.gif" alt="edit" title="{l s='edit' mod='tntcarrier'}"/></a>
			<a href="index.php?tab={$glob.tab}&configure={$glob.configure}&token={$glob.token}&tab_module={$glob.tab_module}&module_name={$glob.module_name}&id_tab=3&section=service&action=del&service={$v.optionId}">
				<img src="../img/admin/delete.gif" alt="delete" title="{l s='delete' mod='tntcarrier'}"/></a></td></tr>
{/foreach}
</table><br/>
<div id="divFormService">
{if ($varService.action == 'edit' || $varService.action == 'new') && $varService.section == 'service'}
{$varService.form}
{/if}
</div>