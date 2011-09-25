{include file='header.tpl'}

	<div id="main">

		<div id="main-precontents"></div>
		<div id="main-contents" class="main-contents">

			<script type="text/javascript">
				{include file='bricks/allcomments.tpl'}
				var g_pageInfo = {ldelim}type: 1, typeId: {$npc.entry}, name: '{$npc.name|escape:"quotes"}'{rdelim};
				g_initPath([0,4,{$npc.type}]);
			</script>

			<table class="infobox">
				<tr><th>{#Quick_Facts#}</th></tr>
				<tr><td><div class="infobox-spacer"></div>
					<ul>
						<li><div>{#Level#}: {if $npc.minlevel<>$npc.maxlevel}{$npc.minlevel} - {/if}{$npc.maxlevel}</div></li>
						<li><div>{#Classification#}: {$npc.rank}</div></li>
						<li><div>{#React#}: <span class="q{if $npc.A==-1}7{elseif $npc.A==1}2{else}{/if}">A</span> <span class="q{if $npc.H==-1}7{elseif $npc.H==1}2{else}{/if}">H</span></div></li>
						<li><div>{#Faction#}: <a href="?faction={$npc.faction_num}">{$npc.faction}</a></div></li>
						<li><div>{#Health#}: {if $npc.minhealth<>$npc.maxhealth}{$npc.minhealth} - {/if}{$npc.maxhealth}</div></li>
{if ($npc.minmana or $npc.maxmana)}
						<li><div>{#Mana#}: {if $npc.minmana<>$npc.maxmana}{$npc.minmana} - {/if}{$npc.maxmana}</div></li>
{/if}
{if ($npc.moneysilver>0) or ($npc.moneygold>0) or ($npc.moneycopper>0)}
						<li><div>{#Wealth#}:{if ($npc.moneygold>0)}
 <span class="moneygold">{$npc.moneygold}</span>{/if}
{if ($npc.moneysilver>0)}
 <span class="moneysilver">{$npc.moneysilver}</span>{/if}
{if ($npc.moneycopper>0)}
 <span class="moneycopper">{$npc.moneycopper}</span>{/if}
</div></li>
{/if}
{if $npc.mindmg > 0 and $npc.maxdmg > 0}
						<li><div>{#Damage#}: {$npc.mindmg} - {$npc.maxdmg}</div></li>
{/if} 
{if $npc.armor > 0}
						<li><div>{#Armor#}: {$npc.armor}</div></li>
{/if} 
					</ul>
				</td></tr>
			</table>

			<div class="text">
				<a href="http://www.wowhead.com/?{$query}" target="_blank" class="button-red"><div><blockquote><i>Wowhead</i></blockquote><span>Wowhead</span></div></a>
				<h1>{$npc.name}{if $npc.subname} &lt;{$npc.subname}&gt;{/if}</h1>
				
{if $zonedata}
				{#This_NPC_can_be_found_in#}
{strip}
				<span id="locations">
					{foreach from=$zonedata item=zone_c name=zone_f}
						<a href="javascript:;" onclick="
							myMapper.update(
								{ldelim}
									zone: {$zone_c.zone},
									coords: [
										{foreach from=$exdata[$smarty.foreach.zone_f.index] item=exdata_c name=exdata_f}
											{if (isset($exdata_c.x) and isset($exdata_c.y))}
												[{$exdata_c.x},{$exdata_c.y},
												{ldelim}
													label:'$<br>
													<div class=q0>
														<small>
															{if $exdata_c.t == 0}
																{#Respawn#}:
																{if isset($exdata_c.r.h)} {$exdata_c.r.h}{#hr#}{/if}
																{if isset($exdata_c.r.m)} {$exdata_c.r.m}{#min#}{/if}
																{if isset($exdata_c.r.s)} {$exdata_c.r.s}{#sec#}{/if}
															{else}
																{#Waypoint#}
															{/if}
														</small>
													</div>',type:'{$exdata_c.t}'
												{rdelim}]
											{if $smarty.foreach.exdata_f.last}{else},{/if}
											{/if}
										{/foreach}
									]
								{rdelim});
							g_setSelectedLink(this, 'mapper'); return false" onmousedown="return false">
							{$zone_c.name}</a>{if $zone_c.count>1}&nbsp;({$zone_c.count}){/if}{if $smarty.foreach.zone_f.last}.{else}, {/if}
					{/foreach}
				</span>
{/strip}
				<div id="mapper-generic"></div>
				<div class="clear"></div>
				<script type="text/javascript">
					var myMapper = new Mapper({ldelim}parent: 'mapper-generic', zone: '{$zonedata[0].zone}'{rdelim});
					gE(ge('locations'), 'a')[0].onclick();
				</script>
{else}
				{#This_NPC_cant_be_found#}
{/if}
				{$npc.article}

				<h2>{#Related#}</h2>

			</div>

			<div id="tabs-generic"></div>

			<div id="listview-generic" class="listview"></div>
<script type="text/javascript">
{if isset($allitems)}{include						file='bricks/allitems_table.tpl'  data=$allitems}{/if}
{if isset($allspells)}{include					file='bricks/allspells_table.tpl' data=$allspells}{/if}
var tabsRelated = new Tabs({ldelim}parent: ge('tabs-generic'){rdelim});
{if isset($npc.sells)}{include 					file='bricks/item_table.tpl'	id='sells'						name='sells'					tabsid='tabsRelated' data=$npc.sells}{/if}
{if isset($npc.drop)}{include 					file='bricks/item_table.tpl'	id='drop'							name='drops'					tabsid='tabsRelated' data=$npc.drop}{/if}
{if isset($npc.pickpocketing)}{include	file='bricks/item_table.tpl'	id='pick-pocketing'		name='pickpocketing'	tabsid='tabsRelated' data=$npc.pickpocketing}{/if}
{if isset($npc.skinning)}{include				file='bricks/item_table.tpl'	id='skinning'					name='skinning'				tabsid='tabsRelated' data=$npc.skinning}{/if}
{if isset($npc.starts)}{include					file='bricks/quest_table.tpl'	id='starts'						name='starts'					tabsid='tabsRelated' data=$npc.starts}{/if}
{if isset($npc.ends)}{include						file='bricks/quest_table.tpl'	id='ends'							name='ends'						tabsid='tabsRelated' data=$npc.ends}{/if}
{if isset($npc.abilities)}{include			file='bricks/spell_table.tpl'	id='abilities'				name='abilities'			tabsid='tabsRelated' data=$npc.abilities}{/if}
{if isset($npc.objectiveof)}{include		file='bricks/quest_table.tpl'	id='objective-of'			name='objectiveof'		tabsid='tabsRelated' data=$npc.objectiveof}{/if}
{if isset($npc.teaches)}{include				file='bricks/spell_table.tpl'	id='teaches-ability'	name='teaches'				tabsid='tabsRelated' data=$npc.teaches}{/if}
new Listview({ldelim}template: 'comment', id: 'comments', name: LANG.tab_comments, tabs: tabsRelated, parent: 'listview-generic', data: lv_comments{rdelim});
tabsRelated.flush();
</script>

			{include file='bricks/contribute.tpl'}

			<div class="clear"></div>
		</div>
	</div>

{include file='footer.tpl'}
