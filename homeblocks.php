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

if (!defined('_PS_VERSION_')) {
    exit;
}

class Homeblocks extends Module
{
    protected $config_form = false;
    public $_html;

    /* Files */
    public $upload_dir = _PS_IMG_DIR_.'homeblocks';
    public $allowedFileExtensions = array('png');

    /** Images */
    public $images = array('image');

    /* Admin Tabs */
    public $tabs = array();


    public function __construct()
    {    
        $this->name = 'homeblocks';
        $this->tab = 'front_office_features';
        $this->version = '1.3.0';
        $this->author = 'blauwfruit';
        $this->need_instance = 1;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Home blocks');
        $this->description = $this->l('Blocks on your homepage!');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        require_once 'classes/HomeBlocksObject.php';
        $this->tabs = array(
            'AdminHomeBlocksObject' => array(
                'main' => 0,
                'display_name' => $this->l('Home blocks'),
            )
        );
        
        if (Tools::getValue('action') == 'updatePositions') {
            $this->ajaxProcessUpdatePositions﻿();
        }        
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        include(dirname(__FILE__).'/sql/install.php');
        include(dirname(__FILE__).'/sql/seeder.php');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayHome');
    }

    public function uninstall()
    {
        foreach ($this->tabs as $tab_name => $item) {
            $tab = new Tab(Tab::getIdFromClassName($tab_name));
            $tab->delete();
        }
        include 'sql/uninstall.php';        
        return parent::uninstall();
    }

    public function getContent()
    {
        $this->context->controller->addCSS($this->_path.'views/css/back.css');
        $this->context->controller->addjQueryPlugin('tagify');

        if (((bool)Tools::isSubmit('submitInsanetopblockModule')) == true) {
            $this->postProcess();
        } elseif(Tools::isSubmit('submitNewBlock')) {
            $this->postProcessBlock();
        } elseif(Tools::isSubmit('updateBlock')) {
            $this->postProcessBlock((int)Tools::getValue('id_homeblocks'));
        } elseif (Tools::isSubmit('delete'.$this->name)) {
            $id = (int) Tools::getValue('id_homeblocks');
            $ds = new HomeBlocksObject($id);
            if ($ds->delete() && $this->deleteImages($id)) {
                $this->_html .= $this->displayConfirmation($this->l('Delete success'));
            } else {
                $this->_html .= $this->displayError($this->l('Unable to delete'));
            }
        }
        if (!$this->getContextShopId()) {
            $this->_html .= $this->displayError($this->l('Select a shop to continue'));
        } else {
            if (Tools::isSubmit('addBlock')) {
                $this->_html .= $this->renderForm('submitNewBlock');
            } elseif (Tools::isSubmit('update'.$this->name)) {
                $this->_html .= $this->renderForm('updateBlock', Tools::getValue('id_homeblocks'));
            } else {                
                $this->_html .= $this->renderBlocks();
            }
        }

        return $this->_html;
    }

    protected function renderForm($form, $id = null)
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->position_identifier﻿ = 'id_homeblocks';
        $helper->identifier = $this->identifier;
        $helper->submit_action = $form;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        if ($id) {
            $homeblocks = new HomeBlocksObject((int)$id);
            $form_block = array(
                'id_homeblocks' => $homeblocks->id,
                'name' => $homeblocks->name,
                'description' => $homeblocks->description,
                'classes' => $homeblocks->classes,
                'image' => $homeblocks->image,
                'link' => $homeblocks->link,
                'background_color' => $homeblocks->background_color,
                'text_color' => $homeblocks->text_color,
                'position' => $homeblocks->position,
                'active' => $homeblocks->active,                
            );
        } else {
            $form_block = array(
                'id_homeblocks' => null,                
                'name' => null,
                'description' => null,
                'classes' => null,
                'image' => null,
                'link' => null,
                'background_color' => null,
                'text_color' => null,
                'position' => null,
                'active' => null,                
            );
        }

        $helper->tpl_vars = array(
            'fields_value' => array_merge($this->getConfigFormValues(), $form_block), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        if ($form == ('submitBlock' || 'updateBlock')) {
            return $helper->generateForm(array($this->getConfigFormBlock()));
        } else {
            return $helper->generateForm(array($this->getConfigForm()));
        }

    }


    public function renderBlocks() {
         $fields_list = array(
            'id_homeblocks' => array(
                'title' => $this->l('ID'),
                'type' => 'text',
            ),
            'name' => array(
                'title' => $this->l('Name of block'),
                'type' => 'text',
            ),
            'classes' => array(
                'title' => $this->l('Class of block'),
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
                'type' => 'text',
            ),
            'background_color' => array(
                'title' => $this->l('Background color'),
                'type' => 'color',
            ),
            'text_color' => array(
                'title' => $this->l('Text color'),
                'type' => 'color',
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'filter_key' => 'a!position',
                'position' => true,
                'align' => 'center',
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
        $helper->position_identifier﻿ = 'id_homeblocks';
        $helper->actions = array('edit', 'delete');
        $helper->show_toolbar = false;
        $helper->imageType = 'png';
        $helper->toolbar_btn['new'] = array(
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&addBlock&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add new')
        );
        $helper->title = $this->l('Blocks');
        $helper->table = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->orderBy = 'position';
        $helper->position_identifier = 'id_homeblocks';

        $list_content = HomeBlocksObject::getAll($this->getContextShopId());
        $helper->listTotal = count($list_content);
        return $helper->generateList($list_content, $fields_list);
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'HOMEBLOCKS_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
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
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter a valid email address'),
                        'name' => 'HOMEBLOCKS_ACCOUNT_EMAIL',
                        'label' => $this->l('Email'),
                    ),
                    array(
                        'type' => 'password',
                        'name' => 'HOMEBLOCKS_ACCOUNT_PASSWORD',
                        'label' => $this->l('Password'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
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
                        'type' => 'textarea',
                        'label' => $this->l('Description'),
                        'lang' => false,
                        'name' => 'description',
                        'cols' => 40,
                        'rows' => 10,
                        'class' => 'rte',
                        'autoload_rte' => true,
                    ),

                    array(
                        'type' => 'text',
                        'label' => $this->l('Class of block'),
                        'name' => 'classes',
                        'desc' => "<p>Enter bootstrap class names, example:</p>
                                    <ul>
                                        <li>col-md-6</li>
                                        <li>col-sm-3</li>
                                        <li>button (to add a button)</li>
                                    </ul>
                                    <p>Or, any class you defined yourself in your code base</p>
                                    "
                    ),
                    array(
                        'type' => 'file',
                        'label' => $this->l('Image'),
                        'name' => 'image',
                    ),  
                array(
                    'type' => 'checkbox',
                    'name' => 'clear_image',
                    'values' => array(
                        'query' => array(
                            array(
                                'id' => 'checkbox',
                                'name' => $this->l('Check if you want to clear the old image in order to use no image'),
                                'label' => $this->l('Clear image'),
                                'val' => '1',
                                'checked' => 'checked'
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Link'),
                        'name' => 'link',
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Background color'),
                        'name' => 'background_color',
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Text color'),
                        'name' => 'text_color',
                    ),
                    array(
                        'type' => 'hidden',
                        'label' => $this->l('Postition'),
                        'name' => 'position',
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active'),
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

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'HOMEBLOCKS_DISPLAY_MULTIPLE' => Configuration::get('HOMEBLOCKS_DISPLAY_MULTIPLE', true),
        );
    }

    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();
        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    public function postProcessBlock($id = null)
    {
        $homeblocks = ($id) ? new HomeBlocksObject((int)$id) : new HomeBlocksObject();
        $homeblocks->id_shop = $this->getContextShopId();
        $homeblocks->name = Tools::getValue('name');
        $homeblocks->description = Tools::getValue('description');
        $homeblocks->link = Tools::getValue('link');
        $homeblocks->background_color = Tools::getValue('background_color');
        $homeblocks->text_color = Tools::getValue('text_color');
        $homeblocks->classes = Tools::getValue('classes');
        $homeblocks->active = (int)Tools::getValue('active');
        if ($homeblocks->id) {
            $homeblocks->update();
        } else {
            $homeblocks->add();
        }

        $moveUploadedFile = false;
        if (!Tools::getValue('clear_image_checkbox')) {
            if ($this->uploadDirExists()) {
                foreach ($_FILES as $key => $value) {
                    if ($value['name']) {
                        $extension = pathinfo($value['name'], PATHINFO_EXTENSION);
                        if (in_array($extension, $this->allowedFileExtensions)) {
                            $homeblocks->{$key} = $homeblocks->id.'_'.$key;
                            if (file_exists($this->upload_dir.'/'.$homeblocks->{$key}.'.'.$extension)) {
                                unlink($this->upload_dir.'/'.$homeblocks->{$key}.'.'.$extension);
                            }
                            $moveUploadedFile = move_uploaded_file(
                                $_FILES[$key]['tmp_name'],
                                $this->upload_dir.'/'.$homeblocks->{$key}.'.'.$extension
                            );
                        } else {
                            $this->_html .= $this->displayError($this->l(sprintf(
                                'Extensions %s is not allowed, only: %s',
                                $extension,
                                implode(', ', $this->allowedFileExtensions
                            ))));
                        }
                    }
                }
            }
        }

        $this->emptyTempImgDir();
        if ($moveUploadedFile) {
            $homeblocks->image = $homeblocks->id.'_image';
        } elseif ((int) Tools::getValue('clear_image_checkbox') == 1) {
            $homeblocks->image = null;
        }
        $homeblocks->save();
    }

    public function deleteImages($id)
    {
        foreach ($this->images as $image) {
            $file = sprintf('%s/%d_%s.png', $this->upload_dir, $id, $image);
            if (file_exists($file)) {
                if (!unlink($file)) {
                    return false;
                }
            }
        }
        return true;
    }

    public function hookDisplayHome()
    {
        $blocks = array();

        foreach (HomeBlocksObject::getBlocks($this->context->shop->id) as $key => $value) {
            $blocks[$key] = $value;
            $blocks[$key]['image'] = $this->getImageUri($value['id_homeblocks'], 'image', $value['image']);
        }

        $this->context->smarty->assign(array('blocks' => $blocks));

        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            return $this->context->smarty->fetch('module:homeblocks/views/templates/hook/block.tpl');
        } else {
            return $this->display(__FILE__, 'views/templates/hook/block.tpl');
        }
    }

    /**
     *  Retrieve URL of image
     *
     *  @return string $url
     */
    public function getImageUri($id, $type, $image)
    {
        if (!$image) {
            return null;
        }

        return in_array($type, $this->images)
            ? sprintf('%simg/%s/%d_%s.png', _PS_BASE_URL_SSL_.__PS_BASE_URI__, $this->name, $id, $type)
            : null;
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

    public function getContextShopId()
    {
        $cookieAll = Context::getContext()->cookie->getAll();
        return substr($cookieAll['shopContext'], 2) ? (int)substr($cookieAll['shopContext'], 2) : null;
    }


    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {

    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function ajaxProcessUpdatePositions﻿()
    {
        $way = (int)(Tools::getValue('way'));
        $id = (int)(Tools::getValue('id'));
        $positions = Tools::getValue('homeblocks');
        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);
            if (isset($pos[2]) && (int)$pos[2] === $id) {
                if ($obj = new HomeBlocksObject((int)$pos[2])) {
                    if (isset($position) && $obj->updatePosition($way, $position)) {
                        echo 'ok position '.(int)$position.' for item '.(int)$pos[1].'\r\n';
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update ID '.(int)$id.' 
                        to position '.(int)$position.' "}';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "This ID '.(int)$id.' can t be loaded"}';
                }
                break;
            }
        }
    }
}
