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

// Необходима функция creatureinfo
require('includes/allnpcs.php');

$smarty->config_load($conf_file, 'npcs');

global $npc_cols;

// Разделяем из запроса класс и подкласс вещей
point_delim($podrazdel, $type, $family);

$cache_str = (empty($type) ? 'x' : intval($type)) . '_' . (empty($family) ? 'x' : intval($family));

if (!$npcs = load_cache(2, $cache_str)) {
    unset($npcs);

    global $UDWBaseconf;
    global $DB;

    $rows = $DB->select('
		SELECT c.?#, c.entry
		{
			, l.name_loc?d as `name_loc`
			, l.subname_loc' . $_SESSION['locale'] . ' as `subname_loc`
		}
		FROM ?_aowow_factiontemplate, ?_creature_template c
		{ LEFT JOIN (?_locales_creature l) ON l.entry=c.entry AND ? }
		WHERE 1=1
			{AND creatureType=?}
			{AND family=?}
			AND factiontemplateID=FactionAlliance
		ORDER BY minlevel DESC, name
		{LIMIT ?d}
		', $npc_cols[0], ($_SESSION['locale'] > 0) ? $_SESSION['locale'] : DBSIMPLE_SKIP, ($_SESSION['locale'] > 0) ? 1 : DBSIMPLE_SKIP, ($type != '') ? $type : DBSIMPLE_SKIP, (isset($family)) ? $family : DBSIMPLE_SKIP, ($UDWBaseconf['limit'] != 0) ? $UDWBaseconf['limit'] : DBSIMPLE_SKIP
    );

    $npcs = array();
    foreach ($rows as $numRow => $row) {
        $npcs[$numRow] = array();
        $npcs[$numRow] = creatureinfo2($row);
    }
    save_cache(5, $cache_str, $npcs);
}

global $page;
$page = array(
    'Mapper' => false,
    'Book' => false,
    'Title' => $smarty->get_config_vars('NPCs'),
    'tab' => 0,
    'type' => 0,
    'typeid' => 0,
    'path' => '[0, 4,' . $type . ',' . $family . ']'
);
$smarty->assign('page', $page);

if (count($npcs >= 0))
    $smarty->assign('npcs', $npcs);
// Количество MySQL запросов
$smarty->assign('mysql', $DB->getStatistics());
// Загружаем страницу
$smarty->display('npcs.tpl');