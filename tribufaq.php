<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once _PS_MODULE_DIR_ . '/tribufaq/classes/ModuleClassUtility.php';
include_once _PS_MODULE_DIR_ . '/tribufaq/src/Entity/TribufaqQuestion.php';
include_once _PS_MODULE_DIR_ . '/tribufaq/src/Entity/TribufaqCategory.php';

class TribuFaq extends Module
{
    protected $queries = [];
    protected $moduleTabs = [];
    public function __construct()
    {
        $this->name = 'tribufaq';
        $this->version = '1.0';
        $this->author = 'Tribu and Co';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Tribu FAQ');
        $this->description = $this->l('Affiche une FAQ catégorisée sur la page d\'accueil');

        $this->moduleTabs = [
            [
                'name'              => $this->l('Gestion FAQ'),
                'class_name'        => 'AdminParentTribufaq',
                'parent_class_name' => 'TRIBU',
                'icon'              => 'help_outline'
            ],
            [
                'name'              => $this->l('Catégories FAQ'),
                'class_name'        => 'AdminTribufaqCategory',
                'parent_class_name' => 'AdminParentTribufaq',
            ],
            [
                'name'              => $this->l('Questions/réponses'),
                'class_name'        => 'AdminTribufaqQuestion',
                'parent_class_name' => 'AdminParentTribufaq',
            ]
        ];
        $this->queries = [
            'tribufaq_question' => 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'tribufaq_question` (
                `id_tribufaq_question` INT(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_tribufaq_category` INT(10) unsigned NOT NULL,
                `date_add` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `active` int(1) unsigned DEFAULT "0",
                PRIMARY KEY (`id_tribufaq_question`)
                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;',
            'tribufaq_question_lang' => 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'tribufaq_question_lang` (
                `id_tribufaq_question` INT(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_lang` int(5) unsigned NOT NULL,
                `question` VARCHAR(255) NOT NULL,
                `response` text NOT NULL,
                PRIMARY KEY (`id_tribufaq_question`,`id_lang`)
                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;',
            'tribufaq_category' => 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'tribufaq_category` (
                `id_tribufaq_category` INT(10) unsigned NOT NULL AUTO_INCREMENT,
                `date_add` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `active` int(1) unsigned DEFAULT "0",
                PRIMARY KEY (`id_tribufaq_category`)
                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;',
            'tribufaq_category_lang' => 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'tribufaq_category_lang` (
                `id_tribufaq_category` INT(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_lang` int(5) unsigned NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                PRIMARY KEY (`id_tribufaq_category`,`id_lang`)
                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;'
        ];
    }

    public function install()
    {
        return (
            parent::install()
            && $this->registerHook('displayHome')
            && $this->registerHook('actionFrontControllerSetMedia')
            && ModuleClassUtility::installSql($this->queries)
            && ModuleClassUtility::installModuleTabs($this->name,$this->moduleTabs)
        );
    }

    public function uninstall()
    {
        return (
            parent::uninstall()
            && ModuleClassUtility::removeTabByClassName('AdminTribufaqCategory')
            && ModuleClassUtility::removeTabByClassName('AdminTribufaqQuestion')
            && ModuleClassUtility::removeTabByClassName('AdminParentTribufaq')
            && ModuleClassUtility::uninstallsql($this->queries)
        );
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet(
            'tribufaq-style',
            $this->_path.'views/css/tribufaq.css',
            [
                'media' => 'all',
                'priority' => 1000,
            ]
        );

        $this->context->controller->registerJavascript(
            'tribufaq-javascript',
            $this->_path.'views/js/tribufaq.js',
            [
                'position' => 'bottom',
                'priority' => 1000,
            ]
        );
    }

    public function hookDisplayHome()
    {

        return $this->display(__FILE__,'displayHome.tpl');
    }
}