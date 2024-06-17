<?php

if (!defined('_PS_VERSION_')) {
    exit;
}
class TribufaqQuestion extends ObjectModel
{
    public $id_tribufaq_question;
    public $id_tribufaq_category;
    public $question;
    public $response;
    public $active;
    public $date_add;

    public static $definition = [
        'table'     => 'tribufaq_question',
        'primary'   => 'id_tribufaq_question',
        'multilang' => true,
        'fields'    => [
            'id_tribufaq_category' => ['type' => self::TYPE_INT, 'required' => true],
            'question'             => ['type' => self::TYPE_STRING, 'required' => true, 'lang' => true, 'validate' => 'isGenericName'],
            'response'             => ['type' => self::TYPE_HTML, 'required' => true, 'lang' => true, 'validate' => 'isCleanHtml'],
            'active'               => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'active' => 'status'],
            'date_add'             => ['type' => self::TYPE_DATE, 'required' => false]
        ],
    ];

    public function getLastFaq($number)
    {
        $query = new DbQuery();
        $query->from('tribufaq_question', 'faq');
        $query->select('*');
        $query->leftJoin('tribufaq_question_lang', 'faql', 'faq.id_tribufaq_question = faql.id_tribufaq_question AND faql.id_lang=' . Context::getContext()->language->id);
        $query->where('faq.active = 1');
        $query->orderBy('date_add DESC');
        $query->limit((int)$number);
        return Db::getInstance()->executeS($query);
    }
}