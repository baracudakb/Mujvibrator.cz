<table class="table" cellspacing="0" cellpading="0">
	<tr>
		<th>{l s='Place' mod='tntcarrier'}</th><th>{l s='Additionnal charge(Euros)' mod='tntcarrier'}</th><th></th>
	</tr>
	<tr>
		<td>{$varCountry.country}</td><td>{$varCountry.overcost}</td>
		<td>
		<a href="index.php?tab={$glob.tab}&configure={$glob.configure}&token={$glob.token}&tab_module={$glob.tab_module}&module_name={$glob.module_name}&id_tab=3&section=country&action=edit&country={$varCountry.country}">
			<img src="../img/admin/edit.gif" alt="edit" title="{l s='edit' mod='tntcarrier'}"/></a>
		</td>
	</tr>
</table>
</table><br/><div id="divFormCountry">
{if ($varCountry.action == 'edit' || $varCountry.action == 'new') && $varCountry.section == 'country'}
{$varCountry.form}
{/if}
</div>