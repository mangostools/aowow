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

// Настройки
error_reporting(E_ALL | E_STRICT);

session_start();
if (!isset($_SESSION['locale']) || !is_int($_SESSION['locale']))
    $_SESSION['locale'] = 0;

require_once 'configs/config.php';
// Для Ajax отключаем debug
$UDWBaseconf['debug'] = false;
// Для Ajax ненужен реалм
$UDWBaseconf['realmd'] = false;
// Настройка БД
global $DB;
require_once('includes/db.php');

$search_query = $_GET['search'];
if (strlen($search_query) < 2)
    exit('["", []]');
$search_query = '%' . str_replace('%', '\%', $search_query) . '%';



echo '["' . str_replace('"', '\"', $_GET['search']) . '", [';

function SideByRace($race) {
    switch ($race) {
        case '0':
            // Для всех?
            return 3;
        case '690':
            // Орда?
            return 2;
        case '1101':
            // Альянс?
            return 1;
        default:
            return 0;
    }
}

// Ищем вещи:
$rows = $DB->select('
	SELECT i.entry, ?#, iconname, quality
	FROM ?_aowow_icons a, ?_item_template i{, ?# l}
	WHERE
		?# LIKE ?
		AND a.id = i.displayid
		{ AND l.entry = i.entry AND ?}
	ORDER BY i.quality DESC, i.name
	LIMIT 3', 
	$_SESSION['locale'] == 0 ? 'name' : 'name_loc' . $_SESSION['locale'], // SELECT
        $_SESSION['locale'] == 0 ? DBSIMPLE_SKIP : 'locales_item', // FROM
        $_SESSION['locale'] == 0 ? 'name' : 'name_loc' . $_SESSION['locale'], // WHERE
        $search_query,
        $_SESSION['locale'] == 0 ? DBSIMPLE_SKIP : 1
);

foreach ($rows as $i => $row)
    $found[$row['name'] . ' (Item)'] = array(
        'type' => 3,
        'entry' => $row['entry'],
        'iconname' => $row['iconname'],
        'quality' => $row['quality']
    );

// Ищем объекты:
$rows = $DB->select('
	SELECT entry, name
	FROM ?_gameobject_template
	WHERE
		(name LIKE ?)
	ORDER BY name
	LIMIT 3
	', $search_query
);

foreach ($rows as $i => $row)
    $found[$row['name'] . ' (Object)'] = array(
        'type' => 2,
        'entry' => $row['entry'],
    );

// Ищем квесты:
$rows = $DB->select('
	SELECT entry, Title, RequiredRaces
	FROM ?_quest_template
	WHERE
		(Title LIKE ?)
	ORDER BY Title
	LIMIT 3
	', $search_query
);

foreach ($rows as $i => $row)
    $found[$row['Title'] . ' (Quest)'] = array(
        'type' => 5,
        'entry' => $row['entry'],
        'side' => SideByRace($row['RequiredRaces'])
    );

// Ищем creature:
$rows = $DB->select('
	SELECT entry, name
	FROM ?_creature_template
	WHERE
		(name LIKE ?)
	ORDER BY name
	LIMIT 3
	', $search_query
);

foreach ($rows as $i => $row)
    $found[$row['name'] . ' (NPC)'] = array(
        'type' => 1,
        'entry' => $row['entry']
    );

// Если ничего не найдено...
if (!IsSet($found)) {
    echo ']]';
    die();
}

ksort($found);

$found = array_slice($found, 0, 10);

$i = 0;
foreach ($found as $name => $fitem) {
    echo '"' . str_replace('"', '\"', $name) . '"';
    if ($i < count($found) - 1)
        echo ', ';
    $i++;
}

echo '], [], [], [], [], [], [';

$i = 0;
foreach ($found as $name => $fitem) {
    echo '[' . $fitem['type'] . ', ' . $fitem['entry'];
    if (IsSet($fitem['iconname']))
        echo ', "' . $fitem['iconname'] . '"';
    if (IsSet($fitem['quality']))
        echo ", " . $fitem['quality'];
    if (IsSet($fitem['side']))
        echo ", " . $fitem['side'];
    echo ']';
    if ($i < count($found) - 1)
        echo ',';
    $i++;
}

echo ']]';