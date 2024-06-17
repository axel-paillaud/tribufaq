<?php

class AdminTribufaqQuestionController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->id_lang = $this->context->language->id;
        $this->table = 'tribufaq_question';
        $this->identifier = 'id_tribufaq_question'; //primary key de la table
        $this->default_form_language = $this->context->language->id;
        $this->controller_name = 'AdminTribufaqQuestionController';
        $this->className = 'TribufaqQuestion';
        $this->lang = true;

        parent::__construct();

        $this->fields_list = [
            'id_tribufaq_question' => [
                'title' => 'ID',
                'align' => 'center',
                'class' => 'fixed-width-xs',

            ],
            'question' => [
                'title' => $this->module->l('Question'),
                'align' => 'left',
                'lang' => true,
            ],

            'response' => [
                'title' => $this->module->l('Answer'),
                'align' => 'left',
                'lang' => true,
            ],

            // TODO show category associated to question/answer
/*             'category' => [
                'title' => $this->module->l('Category'),
                'align' => 'left',
                'lang' => true,
            ], */

            'date_add' => [
                'title' => $this->module->l('Creation date'),
                'align' => 'center',
            ],

            'active' => [
                'title' => $this->module->l('Active'),
                'align' => 'center',
                'type' => 'bool',
                'active' => 'toggleActive',
                'ajax' => true
            ],

        ];

        $this->addRowAction('edit');
        $this->addRowAction('delete');
    }

    public function initContent()
    {
        parent::initContent();
    }

    /**
     * Toolbar settings
     */
    public function initPageHeaderToolbar()
    {
        // 'Add question/answer' button
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
            'desc' => $this->module->l('Ajouter une question/réponse'),
            'icon' => 'process-icon-new'
        );

        parent::initPageHeaderToolbar();
    }

    /**
     * Create and update form
     */
    public function renderForm()
    {
        $this->loadObject(true);

        $this->loadObject(true);
        // Get categories for the dropdown list
        $categories = Db::getInstance()->executeS('SELECT id_tribufaq_category, name FROM ' . _DB_PREFIX_ . 'tribufaq_category_lang WHERE id_lang = ' . (int)$this->context->language->id);

        // Format options for dropdown list
        $category_options = [];
        foreach ($categories as $category) {
            $category_options[] = [
                'id' => $category['id_tribufaq_category'],
                'name' => $category['name']
            ];
        }

        $this->fields_form = [
            'legend' => [
                'title' => $this->module->l('Ajouter une question/réponse'),
                'icon' => 'icon-cog'
            ],

            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->module->l('Question'),
                    'name' => 'question',
                    'lang' => true,
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->module->l('Answer'),
                    'name' => 'response',
                    'lang' => true,
                    'required' => true,
                ],
                [
                    'type' => 'select',
                    'label' => $this->module->l('Category'),
                    'name' => 'id_tribufaq_category',
                    'required' => true,
                    'options' => [
                        'query' => $category_options,
                        'id' => 'id',
                        'name' => 'name'
                    ]
                ],
                [
                    'type' => 'switch',
                    'label' => $this->context->getTranslator()->trans('Active', [], 'Admin.Global'),
                    'name' => 'active',
                    'required' => false,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->context->getTranslator()->trans('Yes', [], 'Admin.Global'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->context->getTranslator()->trans('No', [], 'Admin.Global'),
                        ],
                    ],

                ],

            ],

            'submit' => [
                'title' => $this->context->getTranslator()->trans('Save', [], 'Admin.Actions'),
            ],
        ];

        return parent::renderForm();
    }

    public function ajaxProcessToggleActiveTribufaqCategory()
    {
        $tribufaqQuestion = new TribufaqQuestion(Tools::getValue('id_tribufaq_question'));
        $tribufaqQuestion->active = !$tribufaqQuestion->active;

        if ($tribufaqQuestion->save()) {
            die(Tools::jsonEncode([
                'success' => 1,
                'text' => $this->trans('The settings have been successfully updated.', [], 'Admin.Notifications.Success'),
            ]));
        } else {
            die(Tools::jsonEncode([
                'success' => 0,
                'text' => $this->trans('Unable to update settings.', [], 'Admin.Notifications.Error'),
            ]));
        }
    }
}
