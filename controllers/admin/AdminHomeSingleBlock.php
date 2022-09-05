<?php

class AdminHomeSingleBlockController extends ModuleAdminController
{
    public $name = 'AdminHomeSingleBlock';

    public $_html;

    public function __construct﻿()
    {
        $this->bootstrap = true;
        $this->display = 'AdminHomeSingleBlock';
        $this->meta_title = $this->l('Admin Home Single Block Controller');
        parent::__construct();
    }
    
    public function postProcess()
    {
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

        if (Tools::isSubmit('addBlock')) {
            $this->_html .= $this->renderBlockForm('submitNewBlock');
        } elseif (Tools::isSubmit('update'.$this->name)) {
            $this->_html .= $this->renderBlockForm('updateBlock', Tools::getValue('id_homeblocks'));
        } else {
            $this->_html .= $this->renderBlocks();
        }

        parent::postProcess();
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
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

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
    
    public function display﻿()
    {
        $this->context->smarty->assign(array(
            'test_module_form' => $this->renderForm()
        ));
        parent::display();
    }

    public function postProcessBlock($id = null)
    {
        $homeblocks = ($id) ? new HomeBlocksObject((int)$id) : new HomeBlocksObject();
        $homeblocks->id_shop = $this->module->getContextShopId();
        $homeblocks->name = Tools::getValue('name');
        $homeblocks->active = (int)Tools::getValue('active');
        if ($homeblocks->id) {
            $homeblocks->update();
        } else {
            $homeblocks->add();
        }

        $moveUploadedFile = false;
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
                        if ($mv) {
                            $hasNewImage = true;
                        }
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

        $this->emptyTempImgDir();

        if ($moveUploadedFile) {
            $homeblocks->image = $homeblocks->id.'_image';
        }
        $homeblocks->link = $homeblocks->id.'_link';        
        $homeblocks->save();
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
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&addBlock&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add new')
        );
        $helper->title = $this->l('Blocks');
        $helper->table = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $list_content = HomeBlocksObject::getAll($this->module->getContextShopId());
        $helper->listTotal = count($list_content);
        return $helper->generateList($list_content, $fields_list);
    }
}
