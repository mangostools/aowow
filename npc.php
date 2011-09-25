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

require_once('includes/allspells.php');
require_once('includes/allquests.php');
require_once('includes/allnpcs.php');
require_once('includes/allcomments.php');

// Настраиваем Smarty ;)
$smarty->config_load($conf_file, 'npc');

global $DB;
global $spell_cols;
global $npc_cols;

// Заголовок страницы
$id = $podrazdel;
if (!$npc = load_cache(1, intval($id))) {
    unset($npc);
    // Ищем NPC:
    $npc = array();
    $row = $DB->selectRow('
		SELECT
			?#, c.entry, c.name,
			{
				l.name_loc' . $_SESSION['locale'] . ' as `name_loc`,
				l.subname_loc' . $_SESSION['locale'] . ' as `subname_loc`,
				?,
			}
			f.name_loc' . $_SESSION['locale'] . ' as `faction-name`, ft.factionID as `factionID`
		FROM ?_aowow_factiontemplate ft, ?_aowow_factions f, ?_creature_template c
		{
			LEFT JOIN (?_locales_creature l)
			ON l.entry=c.entry AND ?
		}
		WHERE
			c.entry=?
			AND ft.factiontemplateID=c.faction_A
			AND f.factionID=ft.factionID
		LIMIT 1
			', $npc_cols[1], ($_SESSION['locale'] > 0) ? 1 : DBSIMPLE_SKIP, ($_SESSION['locale'] > 0) ? 1 : DBSIMPLE_SKIP, $id
    );

    if ($row) {
        $npc = $row;
        $npc['name'] = $row['name_loc'] ? $row['name_loc'] : $row['name'];
        $npc['subname'] = $row['subname_loc'] ? $row['subname_loc'] : $row['subname'];
        if ($npc['rank'] == 3) {
            $npc['minlevel'] = '??';
            $npc['maxlevel'] = '??';
        }
        $npc['mindmg'] = ($row['mindmg'] /* + $row['attackpower'] */) * $row['dmg_multiplier'];
        $npc['maxdmg'] = ($row['maxdmg'] /* + $row['attackpower'] */) * $row['dmg_multiplier'];

        $toDiv = array('minhealth', 'maxmana', 'minmana', 'maxhealth', 'armor', 'mindmg', 'maxdmg');
        // Разделяем на тысячи (ххххххххх => ххх,ххх,ххх)
        foreach ($toDiv as $element) {
            $current = array();
            $length = strlen($npc[$element]);
            if ($length <= 3)
                continue;
            $cell1 = $length % 3 > 0 ? $length % 3 : 3;
            $cell = $cell1;
            for ($i = 0; $i < $length / 3; $i++) {
                $pos = $i > 0 ? $cell1 + ($i > 1 ? ($i - 1) * 3 : 0) : 0;
                $current[] = substr($npc[$element], $pos, $cell);
                $cell = 3;
            }
            $npc[$element] = implode(',', $current);
        }

        $npc['rank'] = $smarty->get_config_vars('rank' . $npc['rank']);
        // faction_A = faction_H
        $npc['faction_num'] = $row['factionID'];
        $npc['faction'] = $row['faction-name'];
        // Деньги
        $money = ($row['mingold'] + $row['maxgold']) / 2;
        $npc['moneygold'] = floor($money / 10000);
        $npc['moneysilver'] = floor(($money - ($npc['moneygold'] * 10000)) / 100);
        $npc['moneycopper'] = floor($money - ($npc['moneygold'] * 10000) - ($npc['moneysilver'] * 100));
        // Дроп
        $lootid = $row['lootid'];
        // Используемые спеллы
        $npc['ablities'] = array();
        $tmp = array();
        for ($j = 0; $j <= 4; ++$j) {
            if ($row['spell' . $j] && !in_array($row['spell' . $j], $tmp)) {
                $tmp[] = $row['spell' . $j];
                if ($data = spellinfo($row['spell' . $j], 0)) {
                    if ($data['name'])
                        $npc['abilities'][] = $data;
                }
            }
        }
        for ($j = 1; $j < 4; $j++) {
            $tmp2 = $DB->select('
				SELECT action?d_param1
				FROM ?_creature_ai_scripts
				WHERE
					creature_id=?d
					AND action?d_type=11
				', $j, $npc['entry'], $j
            );
            if ($tmp2)
                foreach ($tmp2 as $i => $tmp3)
                    if (!in_array($tmp2[$i]['action' . $j . '_param1'], $tmp)) {
                        $tmp[] = $tmp2[$i]['action' . $j . '_param1'];
                        if ($data = spellinfo($tmp2[$i]['action' . $j . '_param1'], 0)) {
                            if ($data['name'])
                                $npc['abilities'][] = $data;
                        }
                    }
        }
        if (!$npc['ablities'])
            unset($npc['ablities']);
    }

    // Обучает:
    // Если это пет со способностью:
    $row = $DB->selectRow('
		SELECT Spell1, Spell2, Spell3, Spell4
		FROM ?_petcreateinfo_spell
		WHERE
			entry=?d
		', $npc['entry']
    );
    if ($row) {
        $npc['teaches'] = array();
        for ($j = 1; $j <= 4; $j++)
            if ($row['Spell' . $j])
                for ($k = 1; $k <= 3; $k++) {
                    $spellrow = $DB->selectRow('
						SELECT ?#, spellID
						FROM ?_aowow_spell, ?_aowow_spellicons
						WHERE
							spellID=(SELECT effect' . $k . 'triggerspell FROM ?_aowow_spell WHERE spellID=?d AND (effect' . $k . 'id IN (36,57)))
							AND id=spellicon
						LIMIT 1
						', $spell_cols[2], $row['Spell' . $j]
                    );
                    if ($spellrow) {
                        $num = count($npc['teaches']);
                        $npc['teaches'][$num] = array();
                        $npc['teaches'][$num] = spellinfo2($spellrow);
                    }
                }
    }
    unset($row);

    // Если это просто тренер
    $teachspells = $DB->select('
		SELECT ?#, spellID
		FROM ?_npc_trainer, ?_aowow_spell, ?_aowow_spellicons
		WHERE
			entry=?d
			AND spellID=Spell
			AND id=spellicon
		', $spell_cols[2], $npc['entry']
    );
    if ($teachspells) {
        if (!(IsSet($npc['teaches'])))
            $npc['teaches'] = array();
        foreach ($teachspells as $teachspell) {
            $num = count($npc['teaches']);
            $npc['teaches'][$num] = array();
            $npc['teaches'][$num] = spellinfo2($teachspell);
        }
    }
    unset($teachspells);

    // Продает:
    $rows_s = $DB->select('
		SELECT ?#, i.entry, i.maxcount, n.`maxcount` as `drop-maxcount`
			{, l.name_loc?d AS `name_loc`}
		FROM ?_npc_vendor n, ?_aowow_icons, ?_item_template i
			{LEFT JOIN (?_locales_item l) ON l.entry=i.entry AND ?d}
		WHERE
			n.entry=?
			AND i.entry=n.item
			AND id=i.displayid
		', $item_cols[2], ($_SESSION['locale']) ? $_SESSION['locale'] : DBSIMPLE_SKIP, ($_SESSION['locale']) ? 1 : DBSIMPLE_SKIP, $id
    );
    if ($rows_s) {
        $npc['sells'] = array();
        foreach ($rows_s as $numRow => $row) {
            $npc['sells'][$numRow] = array();
            $npc['sells'][$numRow] = iteminfo2($row);
            $npc['sells'][$numRow]['maxcount'] = $row['drop-maxcount'];
            $npc['sells'][$numRow]['cost'] = array();
            /* if ($row['ExtendedCost']) [NOTE] overwrite with honor points? 
              {
              $extcost = $DB->selectRow('SELECT * FROM ?_aowow_item_extended_cost WHERE extendedcostID=?d LIMIT 1', $row['ExtendedCost']);
              if ($extcost['reqhonorpoints']>0)
              $npc['sells'][$numRow]['cost']['honor'] = (($npc['A']==1)? 1: -1) * $extcost['reqhonorpoints'];
              if ($extcost['reqarenapoints']>0)
              $npc['sells'][$numRow]['cost']['arena'] = $extcost['reqarenapoints'];
              $npc['sells'][$numRow]['cost']['items'] = array();
              for ($j=1;$j<=5;$j++)
              if (($extcost['reqitem'.$j]>0) and ($extcost['reqitemcount'.$j]>0))
              {
              allitemsinfo($extcost['reqitem'.$j], 0);
              $npc['sells'][$numRow]['cost']['items'][] = array('item' => $extcost['reqitem'.$j], 'count' => $extcost['reqitemcount'.$j]);
              }
              } */
            if ($row['BuyPrice'] > 0)
                $npc['sells'][$numRow]['cost']['money'] = $row['BuyPrice'];
        }
        unset($row);
        unset($numRow);
        unset($extcost);
    }
    unset($rows_s);

    // Дроп
    if (!($npc['drop'] = loot('?_creature_loot_template', $lootid)))
        unset($npc['drop']);

    // Кожа
    if (!($npc['skinning'] = loot('?_skinning_loot_template', $lootid)))
        unset($npc['skinning']);

    // Воруеццо
    if (!($npc['pickpocketing'] = loot('?_pickpocketing_loot_template', $lootid)))
        unset($npc['pickpocketing']);

    // Начиниают квесты...
    $rows_qs = $DB->select('
		SELECT ?#
		FROM ?_creature_questrelation c, ?_quest_template q
		WHERE
			c.id=?
			AND q.entry=c.quest
		', $quest_cols[2], $id
    );
    if ($rows_qs) {
        $npc['starts'] = array();
        foreach ($rows_qs as $numRow => $row) {
            $npc['starts'][] = GetQuestInfo($row, 0xFFFFFF);
        }
    }
    unset($rows_qs);

    // Заканчивают квесты...
    $rows_qe = $DB->select('
		SELECT ?#
		FROM ?_creature_involvedrelation c, ?_quest_template q
		WHERE
			c.id=?
			AND q.entry=c.quest
		', $quest_cols[2], $id
    );
    if ($rows_qe) {
        $npc['ends'] = array();
        foreach ($rows_qe as $numRow => $row) {
            $npc['ends'][] = GetQuestInfo($row, 0xFFFFFF);
        }
    }
    unset($rows_qe);

    // Необходимы для квеста..
    $rows_qo = $DB->select('
		SELECT ?#
		FROM ?_quest_template
		WHERE
			ReqCreatureOrGOId1=?
			OR ReqCreatureOrGOId2=?
			OR ReqCreatureOrGOId3=?
			OR ReqCreatureOrGOId4=?
		', $quest_cols[2], $id, $id, $id, $id
    );
    if ($rows_qo) {
        $npc['objectiveof'] = array();
        foreach ($rows_qo as $numRow => $row) {
            $npc['objectiveof'][] = GetQuestInfo($row, 0xFFFFFF);
        }
    }
    unset($rows_qo);

    // Положения созданий божих:
    position($npc['entry'], 'creature');

    save_cache(1, $npc['entry'], $npc);
}

global $page;
$page = array(
    'Mapper' => true,
    'Book' => false,
    'Title' => $npc['name'] . ' - ' . $smarty->get_config_vars('NPCs'),
    'tab' => 0,
    'type' => 1,
    'typeid' => $npc['entry'],
    'path' => '[0,4,' . $npc['type'] . ']'
);

$smarty->assign('page', $page);

// Комментарии
$smarty->assign('comments', getcomments($page['type'], $page['typeid']));

// Если хоть одна информация о вещи найдена - передаём массив с информацией о вещях шаблонизатору
if (IsSet($allitems))
    $smarty->assign('allitems', $allitems);
if (IsSet($allspells))
    $smarty->assign('allspells', $allspells);

$smarty->assign('npc', $npc);

// Количество MySQL запросов
$smarty->assign('mysql', $DB->getStatistics());

// Запускаем шаблонизатор
$smarty->display('npc.tpl');