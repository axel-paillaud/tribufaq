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

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

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

    /**
    * This method handles the module's configuration page
    * @return string The page's HTML content
    */
    public function getContent()
    {
        $output = '';

        // this part is executed only when the form is submitted
        if (Tools::isSubmit('submit' . $this->name)) {
            // retrieve the value set by the user
            $configValue = (string) Tools::getValue('TRIBUFAQ_QUESTIONS_TO_SHOW');

            // check that the value is valid
            if (empty($configValue) || !Validate::isInt($configValue)) {
                // invalid value, show an error
                $output = $this->displayError($this->l('Invalid Configuration value'));
            } else {
                // value is ok, update it and display a confirmation message
                Configuration::updateValue('TRIBUFAQ_QUESTIONS_TO_SHOW', $configValue);
                $output = $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        // display any message, then the form
        return $output . $this->displayForm();
    }

    /**
    * Builds the configuration forms
    * @return string HTML code
    */
    public function displayForm() 
    {
        // Init fields form array
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Number of question/answer to show'),
                        'name' => 'TRIBUFAQ_QUESTIONS_TO_SHOW',
                        'required' => true,
                        'hint' => $this->l('Only numbers are allowed'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->table = $this->table;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
        $helper->submit_action = 'submit' . $this->name;

        // Default language
        $helper->default_form_language = $this->context->language->id;

        // Load current value into the form
        $helper->fields_value['TRIBUFAQ_QUESTIONS_TO_SHOW'] = Tools::getValue('TRIBUFAQ_QUESTIONS_TO_SHOW', Configuration::get('TRIBUFAQ_QUESTIONS_TO_SHOW'));

        return $helper->generateForm([$form]);
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
    // Get number of questions to show per category from the module config
    $questionToShowPerCategory = (int)Configuration::get('TRIBUFAQ_QUESTIONS_TO_SHOW');

    // Get all active categories
    $sqlCategories = '
        SELECT c.id_tribufaq_category, cl.name as category_name
        FROM ' . _DB_PREFIX_ . 'tribufaq_category c
        JOIN ' . _DB_PREFIX_ . 'tribufaq_category_lang cl ON c.id_tribufaq_category = cl.id_tribufaq_category
        WHERE c.active = 1 AND cl.id_lang = ' . (int)$this->context->language->id;

    $categories = Db::getInstance()->executeS($sqlCategories);

    $faqs = [];

    // For each category, limit question based on the config module value
    foreach ($categories as $category) {
        $sqlQuestions = '
            SELECT q.id_tribufaq_question, ql.question, ql.response
            FROM ' . _DB_PREFIX_ . 'tribufaq_question q
            JOIN ' . _DB_PREFIX_ . 'tribufaq_question_lang ql ON q.id_tribufaq_question = ql.id_tribufaq_question
            WHERE q.active = 1 AND q.id_tribufaq_category = ' . (int)$category['id_tribufaq_category'] . '
            AND ql.id_lang = ' . (int)$this->context->language->id . '
            ORDER BY q.date_add DESC
            LIMIT ' . (int)$questionToShowPerCategory;

        $questions = Db::getInstance()->executeS($sqlQuestions);

        // Populate faqs table
        $faqs[] = [
            'category_name' => $category['category_name'],
            'questions' => array_map(function ($question) {
                return [
                    'question' => $question['question'],
                    'response' => $question['response']
                ];
            }, $questions)
        ];
    }
        // Set index keys start from 0
        $faqs = array_values($faqs);

        // Set smarty variable
        $this->context->smarty->assign([
            'faqs' => $faqs,
        ]);

        return $this->display(__FILE__,'displayHome.tpl');
    }
}
