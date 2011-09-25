<?php

/*
 * UDWBase: WOWDB Web Interface
 *
 * © UDW 2009-2011
 *
 * Released under the terms and conditions of the
 * GNU General Public License (http://gnu.org).
 *
 */

// Загружаем новости
$rows = @$DB->select('SELECT text_loc?d AS text FROM ?_aowow_news ORDER BY time DESC, id DESC LIMIT 5', $_SESSION['locale']);
if ($rows)
    $smarty->assign('news', $rows);

$rows2 = @$DB->select('SELECT version AS text FROM ?_db_version LIMIT 1');
if ($rows2)
    $smarty->assign('version', $rows2);

global $page;
$smarty->assign('page', $page);
$smarty->display('main.tpl');