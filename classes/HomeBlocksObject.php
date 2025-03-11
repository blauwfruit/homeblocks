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
    public $image_position;
    public $image_size;
    public $link;
    public $background_color;
    public $text_color;
    public $button_text;
    public $classes;
    public $position;
    public $active;
    
    /**
     * Image positions
     **/
    const IMAGE_POSITION_TOP_LEFT = 'top-left';
    const IMAGE_POSITION_TOP_RIGHT = 'top-right';
    const IMAGE_POSITION_BOTTOM_RIGHT = 'bottom-right';
    const IMAGE_POSITION_BOTTOM_LEFT = 'bottom-left';
    const IMAGE_POSITION_CENTER = 'center';

    /**
     * Image sizes
     **/
    const IMAGE_SIZE_10 = '10';
    const IMAGE_SIZE_25 = '25';
    const IMAGE_SIZE_50 = '50';
    const IMAGE_SIZE_75 = '74';
    const IMAGE_SIZE_COVER = 'cover';
    const IMAGE_SIZE_CONTAIN = 'contain';

    const CSS_VALUES = [
        self::IMAGE_POSITION_TOP_LEFT => 'top left',
        self::IMAGE_POSITION_TOP_RIGHT => 'top right',
        self::IMAGE_POSITION_BOTTOM_RIGHT => 'bottom right',
        self::IMAGE_POSITION_BOTTOM_LEFT => 'bottom left',
        self::IMAGE_POSITION_CENTER => 'center',
        self::IMAGE_SIZE_10 => '10%',
        self::IMAGE_SIZE_25 => '25%',
        self::IMAGE_SIZE_50 => '50%',
        self::IMAGE_SIZE_75 => '74%',
        self::IMAGE_SIZE_COVER => 'cover',
        self::IMAGE_SIZE_CONTAIN => 'contain',
    ];

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
            'image_position'    => array('type' => self::TYPE_STRING),
            'image_size'        => array('type' => self::TYPE_STRING),
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

    public static function getBlocks($id_shop = 0, $images)
    {
        $where = '';
        if ($id_shop) {
            $where = 'AND (id_shop = '. (int) $id_shop .' OR id_shop = 0)';
        }

        $blocks = Db::getInstance()->executeS('
            SELECT * FROM `'. _DB_PREFIX_ . 'homeblocks`
            WHERE active = 1 
            '.$where.'
            ORDER BY position ASC
        ');

        foreach ($blocks as &$block) {
            $block['image'] = HomeBlocksObject::getImageUri($block['id_homeblocks'], 'image', $block['image'], $images);
            $block['image_position'] = HomeBlocksObject::getCssValue($block['image_position']);
            $block['image_size'] = HomeBlocksObject::getCssValue($block['image_size']);
        }

        return $blocks;
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

    /**
     *  Retrieve URL of image
     *
     *  @return string $url
     */
    public static function getImageUri($id, $type, $image, $images)
    {
        if (!$image) {
            return null;
        }

        return in_array($type, $images)
            ? sprintf('%simg/%s/%d_%s.png', _PS_BASE_URL_SSL_.__PS_BASE_URI__, 'homeblocks', $id, $type)
            : null;
    }

    public static function getCssValue($key)
    {
        if (isset(HomeBlocksObject::CSS_VALUES[$key])) {
            return HomeBlocksObject::CSS_VALUES[$key];
        }

        return '';
    }
}
