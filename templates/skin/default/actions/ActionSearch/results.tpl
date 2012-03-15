{include file='header.tpl'}



<h2>{$aLang.search_results}: {$aReq.q|escape:'html'}</h2>

<ul class="switcher">
{foreach from=$aRes.aCounts item=iCount key=sType name="sTypes"}
	<li {if $aReq.sType == $sType}class="active"{/if}>					
		<a href="{router page='search'}{$sType}/?q={$aReq.q|escape:'html'}">
			{$iCount}
			{if $sType=="topics"}
				{$iCount|declension:$aLang.ess_search_results_count_topics:'russian'} 
			{elseif $sType=="comments"}
				{$iCount|declension:$aLang.ess_search_results_count_comments:'russian'} 
			{elseif $sType=="users"}
				{$iCount|declension:$aLang.ess_search_results_count_users:'russian'} 
			{/if}
		</a>
	</li>				
{/foreach}
</ul>

{if $bIsResults}
	{if $aReq.sType == 'topics'}
		{include file='topic_list.tpl'}
	{elseif $aReq.sType == 'comments'}
		{include file='comment_list.tpl'}
	{elseif $aReq.sType == 'users'}
		{include file='sphinx/templates/skin/default/actions/ActionSearch/user.tpl'}
	{/if}
{else}
	{$aLang.search_results_empty}
{/if}


{include file='footer.tpl'}