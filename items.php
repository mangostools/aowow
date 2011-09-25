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

// Необходима функция iteminfo
require_once('includes/allitems.php');

$smarty->config_load($conf_file, 'items');

// Разделяем из запроса класс и подкласс вещей
point_delim($podrazdel, $class, $subclass);

global $DB;

$cache_str = (!isset($class) ? 'x' : intval($class)) . '_' . (!isset($subclass) ? 'x' : intval($subclass));

if (!$items = load_cache(7, $cache_str)) {
    unset($items);

    // Составляем запрос к БД, выполняющий поиск по заданным классу и подклассу
    $rows = $DB->select('
		SELECT ?#, i.entry, maxcount
			{, l.name_loc?d AS `name_loc`}
		FROM ?_aowow_icons, ?_item_template i
			{LEFT JOIN (?_locales_item l) ON l.entry=i.entry AND ?d}
		WHERE
			id=displayid
			{ AND class=? }
			{ AND subclass=? }
			ORDER BY quality DESC, name
			LIMIT 200
		', $item_cols[2], ($_SESSION['locale']) ? $_SESSION['locale'] : DBSIMPLE_SKIP, ($_SESSION['locale']) ? 1 : DBSIMPLE_SKIP, ($class != '') ? $class : DBSIMPLE_SKIP, ($subclass != '') ? $subclass : DBSIMPLE_SKIP
    );

    $i = 0;
    $items = array();
    foreach ($rows as $numRow => $row) {
        $items[$i] = array();
        $items[$i] = iteminfo2($row);
        $i++;
    }

    save_cache(7, $cache_str, $items);
}

global $page;
$page = array(
    'Mapper' => false,
    'Book' => false,
    'Title' => $smarty->get_config_vars('Items'),
    'tab' => 0,
    'type' => 0,
    'typeid' => 0,
    'path' => "[0, 0, " . $class . ", " . $subclass . "]",
);
$smarty->assign('page', $page);

// Статистика выполнения mysql запросов
$smarty->assign('mysql', $DB->getStatistics());
// Если хоть одна информация о вещи найдена - передаём массив с информацией о вещях шаблонизатору
if (count($allitems) >= 0)
    $smarty->assign('allitems', $allitems);
if (count($items >= 0))
    $smarty->assign('items', $items);
// Загружаем страницу
$smarty->display('items.tpl');