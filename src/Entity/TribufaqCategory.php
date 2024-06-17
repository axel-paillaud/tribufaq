<?php

if (!defined('_PS_VERSION_')) {
    exit;
}
class TribufaqCategory extends ObjectModel
{
    public $id_tribufaq_category;
    public $name;
    public $active;
    public $date_add;


    public static $definition = [
        'table'     => 'tribufaq_category',
        'primary'   => 'id_tribufaq_category',
        'multilang' => true,
        'fields'    => [
            'name'     => ['type' => self::TYPE_STRING, 'required' => true, 'lang' => true, 'validate' => 'isGenericName'],
            'active'   => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'active' => 'status'],
            'date_add' => ['type' => self::TYPE_DATE, 'required' => false]
        ],
    ];

    public function getCategoryName($id_tribufaq_category)
    {
        $query = new DbQuery();
        $query->from('tribufaq_category', 'fc');
        $query->select('name');
        $query->leftJoin('tribufaq_category_lang', 'fcl', 'fc.id_tribufaq_category = fcl.id_tribufaq_category AND id_lang=' . Context::getContext()->language->id);
        $query->where('fc.id_tribufaq_category = '.$id_tribufaq_category);
        return Db::getInstance()->getValue($query);
    }

    public function getActiveCategoriesForSelect()
    {
        $query = new DbQuery();
        $query->from('tribufaq_category', 'fc');
        $query->select('fc.id_tribufaq_category as id_category, name');
        $query->leftJoin('tribufaq_category_lang', 'fcl', 'fc.id_tribufaq_category = fcl.id_tribufaq_category');
        $query->where('fc.active = 1 AND fcl.id_lang=' . Context::getContext()->language->id);

        return Db::getInstance()->executeS($query);
    }
}