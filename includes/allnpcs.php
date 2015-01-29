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

// Для списка creatureinfo()
$npc_cols[0] = array('name', 'subname', 'minlevel', 'maxlevel', 'Creaturetype', 'rank', 'FactionAlliance', 'FactionHorde');
$npc_cols[1] = array('subname', 'minlevel', 'maxlevel', 'Creaturetype', 'rank', 'minhealth', 'maxhealth', 'minmana', 'maxmana', 'mingold', 'maxgold', 'lootid', 'spell1', 'spell2', 'spell3', 'spell4', 'FactionAlliance', 'FactionHorde', 'mindmg', 'maxdmg', 'attackpower', 'dmg_multiplier', 'armor');

// Функция информации о создании
/**
 *
 * @param type $Row
 * @return type 
 */
function creatureinfo2(&$Row) {
    // Номер создания
    $creature['entry'] = $Row['entry'];
    // Имя создания
    $creature['name'] = !empty($Row['name_loc']) ? $Row['name_loc'] : $Row['name'];
    // Подимя создания
    $creature['subname'] = !empty($Row['subname_loc']) ? $Row['subname_loc'] : $Row['subname'];
    // Min/Max уровни
    $creature['minlevel'] = $Row['minlevel'];
    $creature['maxlevel'] = $Row['maxlevel'];
    // TODO: Месторасположение
    //	$creature['location'] = location($creature['entry'],'creature');
    // TODO: Реакция на фракции
    $creature['react'] = ($Row['FactionAlliance']) . ',' . ($Row['FactionHorde']);
    // Тип NPC
    $creature['type'] = $Row['Creaturetype'];
    // Тег NPC
    $creature['tag'] = str_normalize($Row['subname']);
    // Ранг NPC
    $creature['classification'] = $Row['rank'];
    return $creature;
}

// Функция информации о создании
/**
 *
 * @param type $id
 * @return type 
 */
function creatureinfo($id) {
    global $DB;
    global $npc_cols;
    $row = $DB->selectRow('
		SELECT ?#, c.entry
		{
			, l.name_loc' . $_SESSION['locale'] . ' as `name_loc`
			, l.subname_loc' . $_SESSION['locale'] . ' as `subname_loc`
			, ?
		}
		FROM ?_aowow_factiontemplate, ?_creature_template c
		{
			LEFT JOIN (?_locales_creature l)
			ON l.entry=c.entry AND ?
		}
		WHERE
			c.entry=?d
			AND factiontemplateID=factionAlliance
		LIMIT 1
		', $npc_cols[0], ($_SESSION['locale'] > 0) ? 1 : DBSIMPLE_SKIP, ($_SESSION['locale'] > 0) ? 1 : DBSIMPLE_SKIP, $id
    );
    return creatureinfo2($row);
}
