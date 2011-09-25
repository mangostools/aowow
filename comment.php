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

require_once ('includes/game.php');

function urlfromtype($type, $typeid) {
    global $types;
    return $types[$type] . '=' . $typeid . '#comments';
}

switch ($_REQUEST["comment"]):
    case 'add':
        // Добавление комментария
        // $_GET["type"] - тип страницы
        // $_GET["typeid"] - номер страницы
        // $_POST['commentbody'] - текст комментария
        // $_POST['replyto'] - номер поста, на который отвечает
        // $_SESSION['userid'] - номер пользователя
        $newid = $DB->query('INSERT
			INTO ?_aowow_comments(`type`, `typeid`, `userid`, `commentbody`, `post_date`{, ?#})
			VALUES (?d, ?d, ?d, ?, NOW(){, ?d})', (empty($_POST['replyto']) ? DBSIMPLE_SKIP : 'replyto'), $_GET["type"], $_GET["typeid"], (empty($_SESSION['userid']) ? 0 : $_SESSION['userid']), stripslashes($_POST['commentbody']), (empty($_POST['replyto']) ? DBSIMPLE_SKIP : $_POST['replyto'])
        );
        if (empty($_POST['replyto']))
            $DB->query('UPDATE ?_aowow_comments SET `replyto`=?d WHERE `id`=?d LIMIT 1', $newid, $newid);
        echo '<meta http-equiv="Refresh" content="0; URL=?' . urlfromtype($_GET["type"], $_GET["typeid"]) . '">';
        echo '<style type="text/css">';
        echo 'body {background-color: black;}';
        echo '</style>';
        break;
    case 'delete':
        // Удаление комментарий (Ajax)
        // Номер комментария: $_GET['id']
        // Имя пользователя, удаляющего комментарий: $_GET['username']
        $DB->query('DELETE FROM ?_aowow_comments WHERE `id`=?d {AND `userid`=?d} LIMIT 1', $_GET['id'], ($_SESSION['roles'] > 1) ? DBSIMPLE_SKIP : $_SESSION['userid']
        );
        break;
    case 'edit':
        // Редактирование комментария
        // Номер комментария: $_GET['id']
        // Новое содержание комментария: $_POST['body']
        // Номер пользователя: $_SESSION['userid']
        if (IsSet($_POST['body']))
            $DB->query('UPDATE ?_aowow_comments SET `commentbody`=?, `edit_userid`=?, `edit_date`=NOW() WHERE `id`=?d {AND `userid`=?d} LIMIT 1', stripslashes($_POST['body']), $_SESSION['userid'], $_GET['id'], ($_SESSION['roles'] > 1) ? DBSIMPLE_SKIP : $_SESSION['userid']
            );
        echo $_POST['body'];
        break;
    case 'rate':
        /*
         * Установка собственоого рейтинга (модераторы и т.п.)
         * Номер комментария: $_GET['id']
         * Новое значение рейтинга: $_GET['rating']
         * Номер пользователя: $_SESSION['userid']
         */
        // Проверка на хоть какое то значение рейтинга, и на то, что пользователь за этот коммент не голосовал
        if (IsSet($_GET['rating']) and !($DB->selectCell('SELECT `commentid` FROM ?_aowow_comments_rates WHERE `userid`=?d AND `commentid`=?d LIMIT 1', $_SESSION['userid'], $_GET['id'])))
            $DB->query('INSERT INTO ?_aowow_comments_rates(`commentid`, `userid`, `rate`) VALUES (?d, ?d, ?d)', $_GET['id'], $_SESSION['userid'], $_GET['rating']);
        break;
    case 'undelete':
    // Восстановление комментария
    // Номер комментария: $_GET['id']
    // Имя пользователя, удаляющего комментарий: $_GET['username']
    default:
        break;
endswitch;