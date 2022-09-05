<?php
/**
*   HomeBlocks
*
*   Do not copy, modify or distribute this document in any form.
*
*   @author     Matthijs <matthijs@blauwfruit.nl>
*   @copyright  Copyright (c) 2013-2019 Blue Raspberry (http://blauwfruit.nl)
*   @license    Proprietary Software
*   @category   FO
*
*/

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'homeblocks` (
    `id_homeblocks` int(11) NOT NULL AUTO_INCREMENT,
    `id_shop` int(11) DEFAULT NULL,
    `name` TEXT DEFAULT NULL,
    `description` LONGTEXT NULL DEFAULT NULL,
    `classes` TEXT DEFAULT NULL,
    `image` TEXT DEFAULT NULL,
    `link` TEXT DEFAULT NULL,
    `button_text` TEXT DEFAULT NULL,
    `position` int(11) DEFAULT NULL,
    `background_color` TEXT DEFAULT NULL,
    `active` int(1) DEFAULT NULL,
    PRIMARY KEY  (`id_homeblocks`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';



foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
