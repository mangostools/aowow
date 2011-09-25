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

require_once ('includes/allobjects.php');
require_once ('includes/allitems.php');
require_once ('includes/allcomments.php');
require_once ('includes/allquests.php');

$smarty->config_load($conf_file, 'object');

// номер объекта;
$id = $podrazdel;

if (!$object = load_cache(3, intval($id))) {
    unset($object);

    // БД
    global $DB;

    // Данные об объекте:
    $object = array();
    $object = objectinfo($id, 1);

    // Начиниают квесты...
    $rows_qs = $DB->select('
		SELECT o.?#
		FROM ?_gameobject_questrelation q, ?_quest_template o
		WHERE
			q.id = ?d
			AND o.entry = q.quest
		', $quest_cols[2], $id
    );
    if ($rows_qs) {
        $object['starts'] = array();
        foreach ($rows_qs as $numRow => $row)
            $object['starts'][] = GetQuestInfo($row, 0xFFFFFF);
    }
    unset($rows_qs);

    // Заканчивают квесты...
    $rows_qe = $DB->select('
		SELECT ?#
		FROM ?_gameobject_involvedrelation i, ?_quest_template q
		WHERE
			i.id = ?d
			AND q.entry = i.quest
		', $quest_cols[2], $id
    );
    if ($rows_qe) {
        $object['ends'] = array();
        foreach ($rows_qe as $numRow => $row)
            $object['ends'][] = GetQuestInfo($row, 0xFFFFFF);
    }
    unset($rows_qe);

    // Положения объектофф:
    position($object['entry'], 'gameobject');

    save_cache(3, $object['entry'], $object);
}

global $page;
$page = array(
    'Mapper' => false,
    'Book' => false,
    'Title' => $object['name'] . ' - ' . $smarty->get_config_vars('Objects'),
    'tab' => 0,
    'type' => 2,
    'typeid' => $object['entry'],
    'path' => '[0,5,' . $object['type'] . ']'
);
if ($object['pagetext'])
    $page['Book'] = true;
$page['Mapper'] = true;

$smarty->assign('page', $page);

// Комментарии
$smarty->assign('comments', getcomments($page['type'], $page['typeid']));

if (isset($allitems))
    $smarty->assign('allitems', $allitems);
if (isset($object))
    $smarty->assign('object', $object);
// Количество MySQL запросов
$smarty->assign('mysql', $DB->getStatistics());
$smarty->display('object.tpl');