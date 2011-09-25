<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
<head>
{include file='head.tpl'}
</head>

<body>
<div id="layers"></div>
<!--[if lte IE 6]><table id="ie6layout"><tr><th class="ie6layout-th"></th><td id="ie6layout-td"><div id="ie6layout-div"></div><![endif]-->
<div id="layout">
	<div id="header">
		<div id="header-logo">
			<a href="."></a>
			<h1>{$page.Title|escape:"html"}</h1>
		</div>
		<div id="header-ad">
			<iframe width="728" height="90" frameborder="0" style="display: none;"/></iframe>
		</div>
	</div>

	<div id="sidebar">
		<div id="sidebar-contents">
			<a href="javascript:;" class="expand-site" title="Purge ads and expand layout" onclick="g_expandSite()"></a>
			<div id="sidebar-ad"><iframe width="160" height="600" frameborder="0" style="display: none;"/></iframe></div>
		</div>
	</div>

	<div id="toplinks" class="toplinks">
		{if isset($user)}
                    <a href="?user={$user.name}">{$user.name}</a>|<a href="?account=signout{if $query}&amp;next={$query|escape}{/if}">{#Sign_out#}</a>
                {else}
                    <a href="?account=signin{if $query}&amp;next={$query|escape}{/if}">{#Sign_in#}</a>
                    <a href="?account=signup{if $query}&amp;next={$query|escape}{/if}">{#Signup#}</a>
                {/if}
                |<a href="javascript:;" id="language-changer">{#Language#} <small>&#9660;</small></a>
	</div>

	<div id="wrapper">
		<div id="toptabs">
			<div id="toptabs-inner">
				<div id="toptabs-right"></div>
				<div id="toptabs-right-generic"></div>
				<div class="clear"></div>
			</div>
		</div>
		<div id="topbar-right">
			<div>
				<form action="?"><a href="javascript:;"></a><input name="search" size="35" value="" id="live-search-generic" /></form>
			</div>
		</div>
		<div id="topbar">
			<span id="menu-buttons-generic" class="menu-buttons"></span>
			<div class="clear"></div>
		</div>

		<script type="text/javascript">g_initHeader({$page.tab}); LiveSearch.attach(ge('live-search-generic'));</script>
