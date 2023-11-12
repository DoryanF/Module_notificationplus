<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class NotificationPlus extends Module
{
    public function __construct()
    {
        $this->name = 'notificationplus';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Doryan Fourrichon';
        $this->ps_versions_compliancy = [
            'min' => '1.6',
            'max' => _PS_VERSION_
        ];
        
        //récupération du fonctionnement du constructeur de la méthode __construct de Module
        parent::__construct();
        $this->bootstrap = true;

        $this->displayName = $this->l('Notification Plus');
        $this->description = $this->l('Module qui envoie des mails pour les administrateurs');

        $this->confirmUninstall = $this->l('Do you want to delete this module');

    }

    public function install()
    {
        if(!parent::install() ||
        !Configuration::updateValue('NEW_CUSTOMER_REGISTER', 0) ||
        !Configuration::updateValue('EMAIL_NEW_CUSTOMER', '') ||
        !Configuration::updateValue('NEW_ORDER_CREATE', 0) ||
        !Configuration::updateValue('EMAIL_NEW_ORDER','') ||
        !Configuration::updateValue('NEW_CUSTOMER_LOGGED', 0) ||
        !Configuration::updateValue('EMAIL_CUSTOMER_LOGGED', '') ||
        !$this->registerHook('actionValidateOrder') ||
        !$this->registerHook('actionCustomerAccountAdd') ||
        !$this->registerHook('actionAuthentication')
        )
        {
            return false;
        }
            return true;

        
    }

    public function uninstall()
    {
        if(!parent::uninstall() ||
        !Configuration::deleteByName('NEW_CUSTOMER_REGISTER') ||
        !Configuration::deleteByName('EMAIL_NEW_CUSTOMER') ||
        !Configuration::deleteByName('NEW_ORDER_CREATE') ||
        !Configuration::deleteByName('EMAIL_NEW_ORDER') ||
        !Configuration::deleteByName('NEW_CUSTOMER_LOGGED') ||
        !Configuration::deleteByName('EMAIL_CUSTOMER_LOGGED') ||
        !$this->unregisterHook('actionValidateOrder') ||
        !$this->unregisterHook('actionCustomerAccountAdd') ||
        !$this->unregisterHook('actionAuthentication')
        )
        {
            return false;
        }
            return true;
    }


    public function getContent()
    {

        return $this->postProcess().$this->renderForm();
    }

    public function renderForm()
    {
        $field_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('Settings Hover Carousel'),
            ],
            'input' => [
                [
                    'type' => 'switch',
                        'label' => $this->l('Active Customer Registrer'),
                        'name' => 'NEW_CUSTOMER_REGISTER',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'label2_on',
                                'value' => 1,
                                'label' => $this->l('Oui')
                            ),
                            array(
                                'id' => 'label2_off',
                                'value' => 0,
                                'label' => $this->l('Non')
                            )
                        )
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Liste des mails admin'),
                    'name' => 'EMAIL_NEW_CUSTOMER',
                    
                ],
                [
                    'type' => 'switch',
                        'label' => $this->l('Active Order Create'),
                        'name' => 'NEW_ORDER_CREATE',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'label2_on',
                                'value' => 1,
                                'label' => $this->l('Oui')
                            ),
                            array(
                                'id' => 'label2_off',
                                'value' => 0,
                                'label' => $this->l('Non')
                            )
                        )
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Liste des mails admin'),
                    'name' => 'EMAIL_NEW_ORDER',
                    
                ],
                [
                    'type' => 'switch',
                        'label' => $this->l('Active Customer Logged'),
                        'name' => 'NEW_CUSTOMER_LOGGED',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'label2_on',
                                'value' => 1,
                                'label' => $this->l('Oui')
                            ),
                            array(
                                'id' => 'label2_off',
                                'value' => 0,
                                'label' => $this->l('Non')
                            )
                        )
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Liste des mails admin'),
                    'name' => 'EMAIL_CUSTOMER_LOGGED',

                ],
            ],
            'submit' => [
                'title' => $this->l('save'),
                'class' => 'btn btn-primary',
                'name' => 'saving'
            ]
        ];

        $helper = new HelperForm();
        $helper->module  = $this;
        $helper->name_controller = $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->fields_value['NEW_CUSTOMER_REGISTER'] = Configuration::get('NEW_CUSTOMER_REGISTER');
        $helper->fields_value['EMAIL_NEW_CUSTOMER'] = Configuration::get('EMAIL_NEW_CUSTOMER');
        $helper->fields_value['NEW_ORDER_CREATE'] = Configuration::get('NEW_ORDER_CREATE');
        $helper->fields_value['EMAIL_NEW_ORDER'] = Configuration::get('EMAIL_NEW_ORDER');
        $helper->fields_value['NEW_CUSTOMER_LOGGED'] = Configuration::get('NEW_CUSTOMER_LOGGED');
        $helper->fields_value['EMAIL_CUSTOMER_LOGGED'] = Configuration::get('EMAIL_CUSTOMER_LOGGED');

        return $helper->generateForm($field_form);
    }

    public function postProcess()
    {
        if(Tools::isSubmit('saving'))
        {
            Configuration::updateValue('NEW_CUSTOMER_REGISTER',Tools::getValue('NEW_CUSTOMER_REGISTER'));
            Configuration::updateValue('EMAIL_NEW_CUSTOMER',Tools::getValue('EMAIL_NEW_CUSTOMER'));
            Configuration::updateValue('NEW_ORDER_CREATE',Tools::getValue('NEW_ORDER_CREATE'));
            Configuration::updateValue('EMAIL_NEW_ORDER',Tools::getValue('EMAIL_NEW_ORDER'));
            Configuration::updateValue('NEW_CUSTOMER_LOGGED',Tools::getValue('NEW_CUSTOMER_LOGGED'));
            Configuration::updateValue('EMAIL_CUSTOMER_LOGGED',Tools::getValue('EMAIL_CUSTOMER_LOGGED'));

            return $this->displayConfirmation('Les champs sont enregistrer !');
        }
    }


    public function hookActionValidateOrder($params)
    {
        if(Configuration::get('NEW_ORDER_CREATE') == 1)
        {
            
            $customer = $params['customer'];
            $order = $params['order'];

            $context = Context::getContext();
            $id_lang = (int) $context->language->id;
            $id_shop = (int) $context->shop->id;
            $configuration = Configuration::getMultiple(
                [
                'PS_SHOP_EMAIL',
                'PS_SHOP_NAME'
                ],$id_lang, null, $id_shop
            );
            $date = date('Y-m-d à H:i:s');

            $tempalte_vars = [
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{email}' => $customer->email,
                '{date}' => $date,
                '{shop_name}' => $configuration['PS_SHOP_EMAIL'],
                '{shop_url}' => 'https://'.$configuration['PS_SHOP_DOMAIN'].'/'.$configuration['PS_SHOP_NAME'],
                '{order_name}' => $order->reference,
                '{payment}' => $order->payment,
                '{total_paid}' => $order->total_paid.'€'
            ];

            $mailsAdmin = explode(', ',Configuration::get('EMAIL_NEW_ORDER'));
            if(!empty($mailsAdmin))
            {
                foreach($mailsAdmin as $mail)
                {
                    $mail_id_lang = $id_lang;

                    Mail::send(
                        $mail_id_lang,
                        'new_order',
                        $this->l('New Order'),
                        $tempalte_vars,
                        $mail,
                        null,
                        null,
                        null,
                        null,
                        null,
                        _PS_MODULE_DIR_.'notificationplus/mails/fr/new_order.html'
                    );
                }
            }
        }
    }

    public function hookActionCustomerAccountAdd($params)
    {
        if(Configuration::get('NEW_CUSTOMER_REGISTER') == 1)
        {
            
            $customer = $params['newCustomer'];

            $context = Context::getContext();
            $id_lang = (int) $context->language->id;
            $id_shop = (int) $context->shop->id;
            $configuration = Configuration::getMultiple(
                [
                'PS_SHOP_EMAIL',
                'PS_SHOP_NAME'
                ],$id_lang, null, $id_shop
            );
            $date = date('Y-m-d à H:i:s');
            
            $tempalte_vars = [
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{email}' => $customer->email,
                '{date}' => $date,
                '{shop_name}' => $configuration['PS_SHOP_EMAIL'],
                '{shop_url}' => 'https://'.$configuration['PS_SHOP_DOMAIN'].'/'.$configuration['PS_SHOP_NAME']
            ];


            $mailsAdmin = explode(', ',Configuration::get('EMAIL_NEW_CUSTOMER'));
            foreach ($mailsAdmin as $mail) {
                $mail_id_lang = $id_lang;

                Mail::send(
                    $mail_id_lang,
                    'new_customer',
                    $this->l('New Customer'),
                    $tempalte_vars,
                    $mail,
                    null,
                    null,
                    null,
                    null,
                    null,
                    _PS_MODULE_DIR_.'notificationplus/mails/fr/new_customer.html'
                );
            }
        }
    }

    public function hookActionAuthentication($params)
    {
        if(Configuration::get('NEW_CUSTOMER_LOGGED') == 1)
        {
            $mailsAdmin = explode(', ',Configuration::get('EMAIL_CUSTOMER_LOGGED'));
            $customer = $params['customer'];

            $context = Context::getContext();
            $id_lang = (int) $context->language->id;
            $id_shop = (int) $context->shop->id;
            $configuration = Configuration::getMultiple(
                [
                'PS_SHOP_EMAIL',
                'PS_SHOP_NAME'
                ],$id_lang, null, $id_shop
            );
            $date = date('Y-m-d à H:i:s');
            

            $tempalte_vars = [
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{email}' => $customer->email,
                '{date}' => $date,
                '{shop_name}' => $configuration['PS_SHOP_EMAIL'],
                '{shop_url}' => 'https://'.$configuration['PS_SHOP_DOMAIN'].'/'.$configuration['PS_SHOP_NAME']
            ];


            foreach ($mailsAdmin as $mail) {
                $mail_id_lang = $id_lang;

                Mail::send(
                    $mail_id_lang,
                    'new_customer_logged',
                    $this->l('New Customer Logged'),
                    $tempalte_vars,
                    $mail,
                    null,
                    null,
                    null,
                    null,
                    null,
                    _PS_MODULE_DIR_.'notificationplus/mails/fr/new_customer_logged.html'
                );
            }
        }
    }
}