{include file='header.tpl'}
{debug}


<h2>{$aLang.search_results}: {$aReq.q|escape:'html'}</h2>

<ul class="switcher">
{foreach from=$aRes.aCounts item=iCount key=sType name="sTypes"}
	<li {if $aReq.sType == $sType}class="active"{/if}>					
		<a href="{router page='search'}{$sType}/?q={$aReq.q|escape:'html'}">
			{$iCount}
			{assign var="sLangKeyType" value="ess_search_results_count_$sType"}
			{$iCount|declension:$aLang[$sLangKeyType]:'russian'} 
			
		</a>
	</li>				
{/foreach}
</ul>

{if $bIsResults}
	{include file="sphinx/templates/skin/default/actions/ActionSearch/{$aReq.sType}.tpl"}
{else}
	{$aLang.search_results_empty}
{/if}


{include file='footer.tpl'}