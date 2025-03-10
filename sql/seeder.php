<?php

$shopId = (int) Context::getContext()->shop->id;

$value = Db::getInstance()->getValue('SELECT id_homeblocks FROM '._DB_PREFIX_.'homeblocks');

if ($value) {
    return true;
}

Db::getInstance()->insert('homeblocks', [
    'id_homeblocks' => 1,
    'id_shop' => $shopId,
    'name' => Module::getInstanceByName('homeblocks')->l('Sale weekend!', 'seeder'),
    'description' => '<p><strong>' . Module::getInstanceByName('homeblocks')->l('Only this weekend, discounts go as low as 50%. That is crazy!', 'seeder') . '</strong></p>',
    'classes' => 'col-md-6 cover',
    'image' => '1_image',
    'link' => './2-home',
    'button_text' => Module::getInstanceByName('homeblocks')->l('Shop now!', 'seeder'),
    'position' => 1,
    'background_color' => '#ffffff',
    'text_color' => '#000000',
    'active' => 1,
]);

Db::getInstance()->insert('homeblocks', [
    'id_homeblocks' => 2,
    'id_shop' => $shopId,
    'name' => Module::getInstanceByName('homeblocks')->l('New Appearel of the Week', 'seeder'),
    'description' => '<p>' . Module::getInstanceByName('homeblocks')->l('The new collection is in! We have some fresh new cotton, leather shoes and sunglasses.', 'seeder') . '</p>',
    'classes' => 'col-md-6 cover',
    'image' => '2_image',
    'link' => './3-cloths',
    'button_text' => Module::getInstanceByName('homeblocks')->l('Shop now!', 'seeder'),
    'position' => 2,
    'background_color' => '#ffffff',
    'text_color' => '#000000',
    'active' => 1,
]);

Db::getInstance()->insert('homeblocks', [
    'id_homeblocks' => 3,
    'id_shop' => $shopId,
    'name' => Module::getInstanceByName('homeblocks')->l('Womens clothing', 'seeder'),
    'description' => '<p>' . Module::getInstanceByName('homeblocks')->l('Hand crafted, and personally handwritten notes.', 'seeder') . '</p>',
    'classes' => 'col-md-6 cover',
    'image' => '3_image',
    'link' => './5-women',
    'button_text' => Module::getInstanceByName('homeblocks')->l('Shop now!', 'seeder'),
    'position' => 3,
    'background_color' => '#ffffff',
    'text_color' => '#000000',
    'active' => 1,
]);

copy(
    _PS_MODULE_DIR_ . 'homeblocks/views/img/seeders/1_image.png',
    _PS_IMG_DIR_ . 'homeblocks/1_image.png'
);

return true;
