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

require_once('includes/game.php');
require_once('includes/allspells.php');
require_once('includes/allquests.php');
require_once('includes/allitems.php');
require_once('includes/allnpcs.php');
require_once('includes/allobjects.php');
require_once('includes/allcomments.php');

// Загружаем файл перевода для smarty
$smarty->config_load($conf_file, 'item');

$id = $podrazdel;
if (!$item = load_cache(5, $id)) {
    unset($item);

    global $DB;

    global $allitems;
    global $allspells;

    global $item_cols;
    global $spell_cols;

    // Информация о вещи...
    $item = iteminfo($podrazdel, 1);

    // Поиск мобов с которых эта вещь лутится
    $drops_cr = drop('?_creature_loot_template', $item['entry']);
    if ($drops_cr) {
        $item['droppedby'] = array();
        foreach ($drops_cr as $lootid => $drop) {
            $rows = $DB->select('
				SELECT c.?#, c.entry
				{
					, l.name_loc?d as `name_loc`
					, l.subname_loc' . $_SESSION['locale'] . ' as `subname_loc`
				}
				FROM ?_aowow_factiontemplate, ?_creature_template c
				{ LEFT JOIN (?_locales_creature l) ON l.entry=c.entry AND ? }
				WHERE
					lootid=?d
					AND factiontemplateID=FactionAlliance
				', $npc_cols[0], ($_SESSION['locale'] > 0) ? $_SESSION['locale'] : DBSIMPLE_SKIP, ($_SESSION['locale'] > 0) ? 1 : DBSIMPLE_SKIP, $lootid
            );
            foreach ($rows as $numRow => $row)
                $item['droppedby'][] = array_merge(creatureinfo2($row), $drop);
        }
        unset($rows);
        unset($lootid);
        unset($drop);
    }
    unset($drops_cr);

    $drops_rf = drop('?_reference_loot_template', $item['entry']);
    if ($drops_rf) {
        foreach ($drops_rf as $refid => $drop) {
            $lrows = $DB->select('SELECT entry,ChanceOrQuestChance FROM ?_creature_loot_template WHERE mincountOrRef = -?d', $refid);
            foreach ($lrows as $numRow => $lrow) {
                // calculate drop rate ( maybe to speedup)
                $loot = loot('?_creature_loot_template', $lrow['entry']);
                foreach ($loot as $info => $value) {
                    if ($value['entry'] == $item['entry'])
                        $drop['percent'] = $value['percent'];
                }

                $rows = $DB->select('
					SELECT c.?#, c.entry
					{
						, l.name_loc?d as `name_loc`
						, l.subname_loc' . $_SESSION['locale'] . ' as `subname_loc`
					}
					FROM ?_aowow_factiontemplate, ?_creature_template c
					{ LEFT JOIN (?_locales_creature l) ON l.entry=c.entry AND ? }
					WHERE
	                                        c.lootid = ?d
						AND factiontemplateID=FactionAlliance
					', $npc_cols[0], ($_SESSION['locale'] > 0) ? $_SESSION['locale'] : DBSIMPLE_SKIP, ($_SESSION['locale'] > 0) ? 1 : DBSIMPLE_SKIP, $lrow['entry']
                );
                foreach ($rows as $numRow => $row)
                    $item['droppedby'][] = array_merge(creatureinfo2($row), $drop);
            }
        }
        unset($rows);
        unset($refid);
        unset($drop);
    }
    unset($drops_rf);

    // Поиск объектов, из которых лутится эта вещь
    $drops_go = drop('?_gameobject_loot_template', $item['entry']);
    if ($drops_go) {
        $item['containedinobject'] = array();
        $item['minedfromobject'] = array();
        $item['gatheredfromobject'] = array();
        foreach ($drops_go as $lootid => $drop) {
            // Сундуки
            $rows = $DB->select('
				SELECT g.entry, g.name, g.type, a.lockproperties1
				FROM ?_gameobject_template g, ?_aowow_lock a
				WHERE
					g.data1=?d
					AND g.type=?d
					AND a.lockID=g.data0
				', $lootid, GAMEOBJECT_TYPE_CHEST, LOCK_PROPERTIES_HERBALISM, LOCK_PROPERTIES_MINING
            );
            foreach ($rows as $numRow => $row) {
                if ($row['lockproperties1'] == LOCK_PROPERTIES_MINING) {
                    // Залежи руды
                    $item['minedfromobject'][] = array_merge(objectinfo2($row), $drop);
                } elseif ($row['lockproperties1'] == LOCK_PROPERTIES_HERBALISM) {
                    // Собирается с трав
                    $item['gatheredfromobject'][] = array_merge(objectinfo2($row), $drop);
                } else {
                    // Сундуки
                    $item['containedinobject'][] = array_merge(objectinfo2($row), $drop);
                }
            }
        }

        if (!($item['containedinobject']))
            unset($item['containedinobject']);
        if (!($item['minedfromobject']))
            unset($item['minedfromobject']);
        if (!($item['gatheredfromobject']))
            unset($item['gatheredfromobject']);

        unset($rows);
    }
    unset($drops_go);

    // Search Vender that sell this thing
    $rows_soldby = $DB->select('
		SELECT ?#, c.entry, v.maxcount AS stock
		{
			, l.name_loc?d as `name_loc`
			, l.subname_loc' . $_SESSION['locale'] . ' as `subname_loc`
		}
		FROM ?_npc_vendor v, ?_aowow_factiontemplate, ?_creature_template c
		{ LEFT JOIN (?_locales_creature l) ON l.entry=c.entry AND ? }
		WHERE
			v.item=?d
			AND c.entry=v.entry
			AND factiontemplateID=FactionAlliance
		ORDER BY 1 DESC, 2 DESC
		', $npc_cols['0'], ($_SESSION['locale'] > 0) ? $_SESSION['locale'] : DBSIMPLE_SKIP, ($_SESSION['locale'] > 0) ? 1 : DBSIMPLE_SKIP, $item['entry']
    );
    if ($rows_soldby) {
        $item['soldby'] = array();
        foreach ($rows_soldby as $numRow => $row) {
            $item['soldby'][$numRow] = array();
            $item['soldby'][$numRow] = creatureinfo2($row);
            $item['soldby'][$numRow]['stock'] = ($row['stock'] == 0) ? -1 : $row['stock'];
            /* [NOTE] implement for honor cost
              if ($row['ExtendedCost'])
              {
              $item['soldby'][$numRow]['cost'] = array();
              $extcost = $DB->selectRow('SELECT * FROM ?_aowow_item_extended_cost WHERE extendedcostID=?d LIMIT 1', $row['ExtendedCost']);
              if ($extcost['reqhonorpoints']>0)
              $item['soldby'][$numRow]['cost']['honor'] = (($row['A']==1)? 1: -1) * $extcost['reqhonorpoints'];
              if ($extcost['reqarenapoints']>0)
              $item['soldby'][$numRow]['cost']['arena'] = $extcost['reqarenapoints'];
              $item['soldby'][$numRow]['cost']['items'] = array();
              for ($j=1;$j<=5;$j++)
              if (($extcost['reqitem'.$j]>0) and ($extcost['reqitemcount'.$j]>0))
              {
              allitemsinfo($extcost['reqitem'.$j], 0);
              $item['soldby'][$numRow]['cost']['items'][] = array('item' => $extcost['reqitem'.$j], 'count' => $extcost['reqitemcount'.$j]);
              }
              } else { */
            $item['soldby'][$numRow]['cost']['money'] = $item['BuyPrice'];
            //}
        }
        unset($extcost);
        unset($numRow);
        unset($row);
    }
    unset($rows_soldby);

    // Поиск квестов, для выполнения которых нужен этот предмет
    $rows_qr = $DB->select('
		SELECT ?#
		FROM ?_quest_template
		WHERE
			ReqItemId1=?d
			OR ReqItemId2=?d
			OR ReqItemId3=?d
			OR ReqItemId4=?d
		', $quest_cols[2], $item['entry'], $item['entry'], $item['entry'], $item['entry']
    );
    if ($rows_qr) {
        $item['objectiveof'] = array();
        foreach ($rows_qr as $numRow => $row)
            $item['objectiveof'][] = GetQuestInfo($row, 0xFFFFFF);
    }
    unset($rows_qr);

    // Поиск квестов, наградой за выполнение которых, является этот предмет
    $rows_qrw = $DB->select('
		SELECT ?#
		FROM ?_quest_template
		WHERE
			RewItemId1=?d
			OR RewItemId2=?d
			OR RewItemId3=?d
			OR RewItemId4=?d
			OR RewChoiceItemId1=?d
			OR RewChoiceItemId2=?d
			OR RewChoiceItemId3=?d
			OR RewChoiceItemId4=?d
			OR RewChoiceItemId5=?d
			OR RewChoiceItemId6=?d
		', $quest_cols[2], $item['entry'], $item['entry'], $item['entry'], $item['entry'], $item['entry'], $item['entry'], $item['entry'], $item['entry'], $item['entry'], $item['entry']
    );
    if ($rows_qrw) {
        $item['rewardof'] = array();
        foreach ($rows_qrw as $numRow => $row)
            $item['rewardof'][] = GetQuestInfo($row, 0xFFFFFF);
    }
    unset($rows_qrw);

    // Поиск вещей, в которых находятся эти вещи
    $drops_cii = drop('?_item_loot_template', $item['entry']);
    if ($drops_cii) {
        $item['containedinitem'] = array();
        foreach ($drops_cii as $lootid => $drop) {
            $rows = $DB->select('
				SELECT c.?#, c.entry, maxcount
				{ , l.name_loc?d AS `name_loc`}
				FROM ?_aowow_icons, ?_item_template c
				{ LEFT JOIN (?_locales_item l) ON l.entry=c.entry AND ? }
				WHERE
					c.entry=?d
					AND id=displayid
				', $item_cols[2], ($_SESSION['locale'] > 0) ? $_SESSION['locale'] : DBSIMPLE_SKIP, ($_SESSION['locale'] > 0) ? 1 : DBSIMPLE_SKIP, $lootid
            );
            foreach ($rows as $numRow => $row)
                $item['containedinitem'][] = array_merge(iteminfo2($row, 0), $drop);
        }
        unset($drops_cii);
        unset($rows);
        unset($lootid);
        unset($drop);
    }

    // Какие вещи содержатся в этой вещи
    if (!($item['contains'] = loot('?_item_loot_template', $item['entry'])))
        unset($item['contains']);

    // Поиск созданий, у которых воруется вещь
    $drops_pp = drop('?_pickpocketing_loot_template', $item['entry']);
    if ($drops_pp) {
        $item['pickpocketingloot'] = array();
        foreach ($drops_pp as $lootid => $drop) {
            $rows = $DB->select('
				SELECT c.?#, c.entry
				{
					, l.name_loc?d as `name_loc`
					, l.subname_loc' . $_SESSION['locale'] . ' as `subname_loc`
				}
				FROM ?_aowow_factiontemplate, ?_creature_template c
				{ LEFT JOIN (?_locales_creature l) ON l.entry=c.entry AND ? }
				WHERE
					pickpocketloot=?d
					AND factiontemplateID=FactionAlliance
				', $npc_cols[0], ($_SESSION['locale'] > 0) ? $_SESSION['locale'] : DBSIMPLE_SKIP, ($_SESSION['locale'] > 0) ? 1 : DBSIMPLE_SKIP, $lootid
            );
            foreach ($rows as $numRow => $row)
                $item['pickpocketingloot'][] = array_merge(creatureinfo2($row), $drop);
        }
        unset($rows);
        unset($lootid);
        unset($drop);
    }
    unset($drops_pp);

    // Поиск созданий, с которых сдираеццо эта шкура
    $drops_sk = drop('?_skinning_loot_template', $item['entry']);
    if ($drops_sk) {
        $item['skinnedfrom'] = array();
        foreach ($drops_sk as $lootid => $drop) {
            $rows = $DB->select('
				SELECT c.?#, c.entry
				{
					, l.name_loc?d as `name_loc`
					, l.subname_loc' . $_SESSION['locale'] . ' as `subname_loc`
				}
				FROM ?_aowow_factiontemplate, ?_creature_template c
				{ LEFT JOIN (?_locales_creature l) ON l.entry=c.entry AND ? }
				WHERE
					skinloot=?d
					AND factiontemplateID=FactionAlliance
				', $npc_cols[0], ($_SESSION['locale'] > 0) ? $_SESSION['locale'] : DBSIMPLE_SKIP, ($_SESSION['locale'] > 0) ? 1 : DBSIMPLE_SKIP, $lootid
            );
            foreach ($rows as $numRow => $row)
                $item['skinnedfrom'][] = array_merge(creatureinfo2($row), $drop);
        }
        unset($rows);
        unset($lootid);
        unset($drop);
    }
    unset($drops_sk);

    // Дизенчантитcя в:
    if (!($item['disenchanting'] = loot('?_disenchant_loot_template', $item['DisenchantID'])))
        unset($item['disenchanting']);

    // Получается дизэнчантом из..
    $drops_de = drop('?_disenchant_loot_template', $item['entry']);
    if ($drops_de) {
        $item['disenchantedfrom'] = array();
        foreach ($drops_de as $lootid => $drop) {
            $rows = $DB->select('
				SELECT c.?#, c.entry, maxcount
				{
					, l.name_loc?d as `name_loc`
				}
				FROM ?_aowow_icons, ?_item_template c
				{ LEFT JOIN (?_locales_item l) ON l.entry=c.entry AND ? }
				WHERE
					DisenchantID=?d
					AND id=displayid
				', $item_cols[2], ($_SESSION['locale'] > 0) ? $_SESSION['locale'] : DBSIMPLE_SKIP, ($_SESSION['locale'] > 0) ? 1 : DBSIMPLE_SKIP, $lootid
            );
            foreach ($rows as $numRow => $row)
                $item['disenchantedfrom'][] = array_merge(iteminfo2($row, 0), $drop);
        }
        unset($rows);
        unset($lootid);
        unset($drop);
    }
    unset($drops_de);

    // Поиск сумок в которые эту вещь можно положить
    if ($item['BagFamily'] == 256) {
        // Если это ключ
        $item['key'] = true;
    } elseif ($item['BagFamily'] > 0 and $item['ContainerSlots'] == 0) {
        $rows_cpi = $DB->select('
			SELECT c.?#, c.entry, maxcount
			{
				, l.name_loc?d as `name_loc`
			}
			FROM ?_aowow_icons, ?_item_template c
			{ LEFT JOIN (?_locales_item l) ON l.entry=c.entry AND ? }
			WHERE
				BagFamily=?d
				AND ContainerSlots>0
				AND id=displayid
			', $item_cols[2], ($_SESSION['locale'] > 0) ? $_SESSION['locale'] : DBSIMPLE_SKIP, ($_SESSION['locale'] > 0) ? 1 : DBSIMPLE_SKIP, $item['BagFamily']
        );
        if ($rows_cpi) {
            $item['canbeplacedin'] = array();
            foreach ($rows_cpi as $numRow => $row)
                $item['canbeplacedin'][] = iteminfo2($row, 0);
        }
        unset($rows_cpi);
    }

    // Реагент для...
    $rows_r = $DB->select('
		SELECT ?#, spellID
		FROM ?_aowow_spell s, ?_aowow_spellicons i
		WHERE
			(( reagent1=?d
			OR reagent2=?d
			OR reagent3=?d
			OR reagent4=?d
			OR reagent5=?d
			OR reagent6=?d
			OR reagent7=?d
			OR reagent8=?d
			) AND ( i.id=s.spellicon))
		', $spell_cols[2], $item['entry'], $item['entry'], $item['entry'], $item['entry'], $item['entry'], $item['entry'], $item['entry'], $item['entry']
    );
    if ($rows_r) {
        $item['reagentfor'] = array();
        $quality = 1;
        foreach ($rows_r as $numRow => $row) {
            $item['reagentfor'][$numRow] = array();
            $item['reagentfor'][$numRow]['entry'] = $row['spellID'];
            $item['reagentfor'][$numRow]['name'] = $row['spellname_loc' . $_SESSION['locale']];
            $item['reagentfor'][$numRow]['school'] = $row['resistancesID'];
            $item['reagentfor'][$numRow]['level'] = $row['levelspell'];
            $item['reagentfor'][$numRow]['quality'] = '@';
            for ($j = 1; $j <= 8; $j++)
                if ($row['reagent' . $j]) {
                    $item['reagentfor'][$numRow]['reagents'][]['entry'] = $row['reagent' . $j];
                    $item['reagentfor'][$numRow]['reagents'][count($item['reagentfor'][$numRow]['reagents']) - 1]['count'] = $row['reagentcount' . $j];
                    allitemsinfo($row['reagent' . $j], 0);
                }
            for ($j = 1; $j <= 3; $j++)
                if ($row['effect' . $j . 'itemtype']) {
                    $item['reagentfor'][$numRow]['creates'][]['entry'] = $row['effect' . $j . 'itemtype'];
                    $item['reagentfor'][$numRow]['creates'][count($item['reagentfor'][$numRow]['creates']) - 1]['count'] = 1 + $row['effect' . $j . 'BasePoints'];
                    allitemsinfo($row['effect' . $j . 'itemtype'], 0);
                    @$item['reagentfor'][$numRow]['quality'] = 6 - $allitems[$row['effect' . $j . 'itemtype']]['quality'];
                }
            // Добавляем в таблицу спеллов
            allspellsinfo2($row);
        }
        unset($quality);
    }
    unset($rows_r);

    // Создается из...
    $rows_cf = $DB->select('
		SELECT ?#, s.spellID
		FROM ?_aowow_spell s, ?_aowow_spellicons i
		WHERE
			((s.effect1itemtype=?d
			OR s.effect2itemtype=?d
			OR s.effect3itemtype=?)
			AND (i.id = s.spellicon))
		', $spell_cols[2], $item['entry'], $item['entry'], $item['entry']
    );
    if ($rows_cf) {
        $item['createdfrom'] = array();
        foreach ($rows_cf as $numRow => $row) {
            $skillrow = $DB->selectRow('
				SELECT skillID, min_value, max_value
				FROM ?_aowow_skill_line_ability
				WHERE spellID=?d
				LIMIT 1', $row['spellID']
            );
            $item['createdfrom'][] = spellinfo2(array_merge($row, $skillrow));
        }
        unset($skillrow);
    }
    unset($rows_cf);

    // Ловится в ...
    $drops_fi = drop('?_fishing_loot_template', $item['entry']);
    if ($drops_fi) {
        $item['fishedin'] = array();
        foreach ($drops_fi as $lootid => $drop) {
            // Обычные локации
            $row = $DB->selectRow('
				SELECT name_loc' . $_SESSION['locale'] . ' AS name, areatableID as id
				FROM ?_aowow_zones
				WHERE
					areatableID=?d
					AND (x_min!=0 AND x_max!=0 AND y_min!=0 AND y_max!=0)
				LIMIT 1
				', $lootid
            );
            if ($row) {
                $item['fishedin'][] = array_merge($row, $drop);
            } else {
                // Инсты
                $row = $DB->selectRow('
					SELECT name_loc' . $_SESSION['locale'] . ' AS name, mapID as id
					FROM ?_aowow_zones
					WHERE
						areatableID=?d
					LIMIT 1
					', $lootid
                );
                if ($row)
                    $item['fishedin'][] = array_merge($row, $drop);
            }
        }
        unset($row);
        unset($num);
    }
    unset($drops_fi);

    save_cache(5, $item['entry'], $item);
}
global $page;
$page = array(
    'Mapper' => false,
    'Book' => false,
    'Title' => $item['name'] . ' - ' . $smarty->get_config_vars('Items'),
    'tab' => 0,
    'type' => 3,
    'typeid' => $item['entry'],
    'path' => '[0,0,' . $item['classs'] . ',' . $item['subclass'] . ']',
);
$smarty->assign('page', $page);

// Комментарии
$smarty->assign('comments', getcomments($page['type'], $page['typeid']));

// Количество MySQL запросов
$smarty->assign('mysql', $DB->getStatistics());
if (IsSet($allitems))
    $smarty->assign('allitems', $allitems);
if (IsSet($allspells))
    $smarty->assign('allspells', $allspells);
$smarty->assign('item', $item);
$smarty->display('item.tpl');