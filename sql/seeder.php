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
    'classes' => 'col-md-6',
    'image' => '1_image',
    'image_position' => HomeBlocksObject::IMAGE_POSITION_BOTTOM_RIGHT,
    'image_size' => HomeBlocksObject::IMAGE_SIZE_25,
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
    'description' => '<p><strong>' . Module::getInstanceByName('homeblocks')->l('The new collection is in! We have some fresh new cotton, leather shoes and sunglasses.', 'seeder') . '</strong></p>',
    'classes' => 'col-md-6',
    'image' => '2_image',
    'image_position' => HomeBlocksObject::IMAGE_POSITION_CENTER,
    'image_size' => HomeBlocksObject::IMAGE_SIZE_COVER,
    'link' => './3-cloths',
    'button_text' => Module::getInstanceByName('homeblocks')->l('Shop now!', 'seeder'),
    'position' => 2,
    'background_color' => '#ffffff',
    'text_color' => '#fff',
    'active' => 1,
]);

Db::getInstance()->insert('homeblocks', [
    'id_homeblocks' => 3,
    'id_shop' => $shopId,
    'name' => Module::getInstanceByName('homeblocks')->l('Womens clothing', 'seeder'),
    'description' => '<p>' . Module::getInstanceByName('homeblocks')->l('Hand crafted, and personally handwritten notes.', 'seeder') . '</p>',
    'classes' => 'col-md-4',
    'image' => '3_image',
    'image_position' => HomeBlocksObject::IMAGE_POSITION_CENTER,
    'image_size' => HomeBlocksObject::IMAGE_SIZE_COVER,
    'link' => './5-women',
    'button_text' => Module::getInstanceByName('homeblocks')->l('Shop now!', 'seeder'),
    'position' => 3,
    'background_color' => '#ffffff',
    'text_color' => '#fff',
    'active' => 1,
]);

Db::getInstance()->insert('homeblocks', [
    'id_homeblocks' => 4,
    'id_shop' => $shopId,
    'name' => Module::getInstanceByName('homeblocks')->l('Womens clothing', 'seeder'),
    'description' => '<p>' . Module::getInstanceByName('homeblocks')->l('Hand crafted, and personally handwritten notes.', 'seeder') . '</p>',
    'classes' => 'col-md-8 button',
    'image' => '3_image',
    'image_position' => HomeBlocksObject::IMAGE_POSITION_BOTTOM_RIGHT,
    'image_size' => HomeBlocksObject::IMAGE_SIZE_50,
    'link' => './5-women',
    'button_text' => Module::getInstanceByName('homeblocks')->l('Shop now!', 'seeder'),
    'position' => 4,
    'background_color' => '#ffffff',
    'text_color' => '#000000',
    'active' => 1,
]);

if (!file_exists(_PS_IMG_DIR_ . 'homeblocks')) {
    mkdir(_PS_IMG_DIR_ . 'homeblocks', 0755, true);
}

copy(
    _PS_MODULE_DIR_ . 'homeblocks/views/img/seeders/1_image.png',
    _PS_IMG_DIR_ . 'homeblocks/1_image.png'
);
copy(
    _PS_MODULE_DIR_ . 'homeblocks/views/img/seeders/2_image.png',
    _PS_IMG_DIR_ . 'homeblocks/2_image.png'
);
copy(
    _PS_MODULE_DIR_ . 'homeblocks/views/img/seeders/3_image.png',
    _PS_IMG_DIR_ . 'homeblocks/3_image.png'
);
copy(
    _PS_MODULE_DIR_ . 'homeblocks/views/img/seeders/1_image.png',
    _PS_IMG_DIR_ . 'homeblocks/4_image.png'
);

return true;
