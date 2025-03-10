<?php
/**
*   HomeBlocksObject
*
*   Do not copy, modify or distribute this document in any form.
*
*   @author     Matthijs <matthijs@blauwfruit.nl>
*   @copyright  Copyright (c) 2013-2019 blauwfruit (http://blauwfruit.nl)
*   @license    Proprietary Software
*   @category   Blocks!
*
*/

/**
 * HomeBlocksObject
 */
class HomeBlocksObject extends ObjectModel
{
    public $id_homeblocks;
    public $id_shop;
    public $name;
    public $description;
    public $image;
    public $link;
    public $background_color;
    public $text_color;
    public $button_text;
    public $classes;
    public $position;
    public $active;
    
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'homeblocks',
        'primary' => 'id_homeblocks',
        'fields' => array(
            'id_shop'           => array('type' => self::TYPE_INT),
            'name'              => array('type' => self::TYPE_STRING),
            'description'       => array('type' => self::TYPE_HTML),
            'classes'           => array('type' => self::TYPE_STRING),
            'image'             => array('type' => self::TYPE_STRING),
            'link'              => array('type' => self::TYPE_STRING),
            'background_color'  => array('type' => self::TYPE_STRING),
            'text_color'        => array('type' => self::TYPE_STRING),
            'button_text'       => array('type' => self::TYPE_STRING),
            'position'          => array('type' => self::TYPE_INT),
            'active'            => array('type' => self::TYPE_INT),
        ),
    );

    /**
     *  Get all blocks in shop context
     *  @param  (int)   $id_shop
     *  @return (array) all blocks 
     */
    public static function getAll($id_shop)
    {
        $where = $id_shop
            ? 'WHERE id_shop = '. (int)$id_shop . ' OR id_shop = 0'
            : '';

        return Db::getInstance()->executeS('
            SELECT * FROM `'. _DB_PREFIX_ . 'homeblocks`
            '. $where .'
            ORDER BY position ASC');
    }

    /**
     *  Get active ones, for use in front-end
     *  @return  (array) blocks
     */
    public static function getAllActive()
    {
        return Db::getInstance()->executeS('SELECT * FROM `'. _DB_PREFIX_ . 'homeblocks`
            WHERE active = 1
            ORDER BY position ASC');
    }

    public static function getBlocks($id_shop = 0)
    {
        if ($id_shop) {
            $where = 'AND (id_shop = '. (int)$id_shop .' OR id_shop = 0)';
        } else {
            $where = '';
        }
        return Db::getInstance()->executeS('
            SELECT * FROM `'. _DB_PREFIX_ . 'homeblocks`
            WHERE active = 1 
            '.$where.'
            ORDER BY position ASC
        ');
    }

    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS(
            'SELECT `id_homeblocks`, `position`
            FROM `'._DB_PREFIX_.'homeblocks`
            ORDER BY `position` ASC'
        )) {
            return false;
        }

        foreach ($res as $item) {
            if ((int)$item['id_homeblocks'] == (int)$this->id) {
                $movedItem = $item;
            }
        }

        if (!isset($movedItem) || !isset($position)) {
            return false;
        }

        return (Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'homeblocks`
            SET `position`= `position` '.($way ? '- 1' : '+ 1').'
            WHERE `position`
            '.($way
            ? '> '.(int)$movedItem['position'].' AND `position` <= '.(int)$position
            : '< '.(int)$movedItem['position'].' AND `position` >= '.(int)$position.''))
            && Db::getInstance()->execute('
                UPDATE `'._DB_PREFIX_.'homeblocks`
                SET `position` = '.(int)$position.'
                WHERE `id_homeblocks` = '.(int)$movedItem['id_homeblocks']));
    }

}
