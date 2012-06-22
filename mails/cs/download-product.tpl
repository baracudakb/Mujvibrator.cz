<ul>
{foreach from=$virtualProducts item=product}
	<li>
		<a href="{$product.link}">{$product.name}</a>
		{if isset($product.deadline)}
			vyprší {$product.deadline}
		{/if}
		{if isset($product.downloadable)}
			je možno stáhnout {$product.downloadable} krát
		{/if}
	</li>
{/foreach}
</ul>