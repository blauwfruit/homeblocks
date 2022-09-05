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
* AdminProductCommentsBlrbLinkController
*/
class AdminHomeBlocksController extends ModuleAdminController
{
	public $name = 'AdminHomeBlocks';

    protected $config_form = false;
    public $_html;

    /**
     *  Files
     */
    public $upload_dir = _PS_IMG_DIR_.'homeblocks';
    public $allowedFileExtensions = array('jpg');

    /**
     *  Images
     */
    public $images = array('image', 'link');

    /**
     *  Position
     */
    protected $position_identifier = 'id_homeblocks';


    // public function __construct() {
    // 	parent::__construct();
    //     $module_link = $this->context->link->getAdminLink($this->name, true)
    //         .'&configure=homeblocks&tab_module=front_office_features&module_name=homeblocks';
    //     Tools::redirectAdmin($module_link);
    // }

    public function __construct() {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function init()
    {
        parent::init();

        $this->context->controller->addJS($this->module->path.'views/js/back.js');
        $this->context->controller->addCSS($this->module->path.'views/css/back.css');
        $this->context->controller->addjQueryPlugin('tagify');

        if (((bool)Tools::isSubmit('submit' . $this->name)) == true) {
            $this->postProcess();
        } elseif(Tools::isSubmit('submit' . $this->name)) {
            $this->postProcessBlock();
        } elseif(Tools::isSubmit('update' . $this->name)) {
            $this->postProcessBlock((int)Tools::getValue('id_homeblocks'));
        } elseif (Tools::isSubmit('delete' . $this->name)) {
            $id = (int) Tools::getValue('id_homeblocks');
            $ds = new HomeBlocksObject($id);
            if ($ds->delete() && $this->deleteImages($id)) {
                $this->_html .= $this->displayConfirmation($this->l('Delete success'));
            } else {
                $this->_html .= $this->displayError($this->l('Unable to delete'));
            }
        }

        if (Tools::isSubmit('add' . $this->name)) {
            $this->_html .= $this->renderBlockForm('submitNewBlock');
        } elseif (Tools::isSubmit('update'.$this->name)) {
            $this->_html .= $this->renderBlockForm('updateBlock', Tools::getValue('id_homeblocks'));
        } else {
            $this->_html .= $this->renderListBlocks();
        }

        $this->context->smarty->assign('module_dir', $this->module->path);

        // $output = $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/configure.tpl');

        return $this->_html;
    }

    public function renderListBlocks()
    {
         $fields_list = array(
            'id_homeblocks' => array(
                'title' => $this->l('ID'),
                'type' => 'text',
            ),
            'name' => array(
                'title' => $this->l('Name of block'),
                'type' => 'text',
            ),
            'image' => array(
                'title' => $this->l('Image'),
                'type' => 'image',
                'image' => $this->name,
                'image_id' => 'image', 
            ),
            'link' => array(
                'title' => $this->l('Link'),
                'type' => 'image',
                'image' => $this->name,
                'image_id' => 'link', 
            ),
            'position' => array(
                'title' => $this->l('Position'),
                // 'filter_key' => 'a!position',
                'position' => true,
                'align' => 'center',
                'classes' => 'fixed-width-md'
            ),            
            'active' => array(
                'title' => $this->l('active'),
                'type' => 'text',
            ),
        );

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->no_link = true;
        $helper->list_no_link = false;
        $helper->identifier = 'id_homeblocks';
        $helper->position_identifier﻿ = 'position';
        $helper->actions = array('edit', 'delete');
        $helper->show_toolbar = false;
        $helper->imageType = 'jpg';
        $helper->toolbar_btn['new'] = array(
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&add'.$this->name.'&token='.Tools::getAdminTokenLite($this->name),
            'desc' => $this->l('Add new')
        );
        $helper->title = $this->l('Blocks');
        $helper->table = $this->name;
        $helper->token = Tools::getAdminTokenLite($this->name);
        $helper->currentIndex = AdminController::$currentIndex.'&controller=' . $this->name;
         // AdminController::$currentIndex . '&configure=' . $this->name;
        $list_content = HomeBlocksObject::getAll($this->getContextShopId());
        $helper->listTotal = count($list_content);
        return $helper->generateList($list_content, $fields_list);
    }

    public function getContextShopId()
    {
        $cookieAll = Context::getContext()->cookie->getAll();
        $shopContext = $cookieAll['shopContext'];
        return (int)substr($shopContext, 2);
    }

    public function renderBlockForm($form, $id = null)
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->position_identifier﻿ = 'position';
        $helper->identifier = $this->identifier;
        $helper->submit_action = $form;
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.
            $this->name.'&tab_module='.$this->module->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite($this->name);

        if ($id) {
            $homeblocks = new HomeBlocksObject((int)$id);
            $form_block = array(
                'id_homeblocks' => $homeblocks->id,
                'name' => $homeblocks->name,
                'image' => $homeblocks->image,
                'link' => $homeblocks->link,
                'position' => $homeblocks->position,
                'active' => $homeblocks->active,                
            );
        } else {
            $form_block = array(
                'id_homeblocks' => null,                
                'name' => null,
                'image' => null,
                'link' => null,
                'position' => null,
                'active' => null,                
            );
        }

        $helper->tpl_vars = array(
            'fields_value' => $form_block, /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        if ($form == ('submit' . $this->name || 'update' . $this->name)) {
            return $helper->generateForm(array($this->getConfigFormBlock()));
        } else {
            return $helper->generateForm(array($this->getConfigForm()));
        }

    }




    public function getListContent()
    {
        return Db::getInstance()->executeS('SELECT * FROM `'. _DB_PREFIX_ . 'homeblocks`');
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigFormBlock()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'id_homeblocks',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Name of the block'),
                        'name' => 'name',
                        'desc' => $this->l('Enter product id\'s separated with comma\'s')
                    ),
                    array(
                        'type' => 'file',
                        'label' => $this->l('image'),
                        'name' => 'image',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('link'),
                        'name' => 'link',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Postition'),
                        'name' => 'position',
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('active'),
                        'name' => 'active',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),

                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }


    public function deleteImages($id)
    {
        foreach ($this->images as $image) {
            $file = sprintf('%s/%d_%s.jpg', $this->upload_dir, $id, $image);
            if (file_exists($file)) {
                if (!unlink($file)) {
                    return false;
                }
            }
        }
        return true;
    }
    public function getIds($string)
    {
        $array = array_map(function($item){
            return (int)trim($item);
        }, explode(',', $string));
        $array = array_unique($array);
        asort($array);
        return $array;
    }

    public function uploadDirExists()
    {
        return file_exists($this->upload_dir) ? true :  mkdir($this->upload_dir);
    }

    public function emptyTempImgDir()
    {
        $temp_img_dir = _PS_IMG_DIR_.'/tmp/';
        if (file_exists($temp_img_dir)) {
            foreach (scandir($temp_img_dir) as $file) {
                if (strpos($file, $this->name) !== false) {
                    unlink($temp_img_dir.$file);
                }
            }
        }
    }


}
