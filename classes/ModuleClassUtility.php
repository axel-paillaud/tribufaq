<?php

class ModuleClassUtility {

    public static function installModuleTabs($moduleName,$tabs)
    {
        //** @function to install the tab

        $id_tab_tribu = Tab::getIdFromClassName("TRIBU");
        // create Tribu tab if not exists
        if(!$id_tab_tribu) {
            $tabToCreate = new Tab();
            $tabToCreate->class_name = "TRIBU";
            $tabToCreate->active = 1;
            $tabToCreate->name[(int)(Configuration::get('PS_LANG_DEFAULT'))]  = "TRIBU AND CO";
            $tabToCreate->id_parent = 0;
            $tabToCreate->module = $moduleName;
            if(!$tabToCreate->add()){
                return false;
            }
        }

        // create the new tabs if not exists
        foreach ($tabs as $tab){
            $id_new_tab = Tab::getIdFromClassName($tab['class_name']);
            if(!$id_new_tab) {
                $subtabToCreate = new Tab();
                $subtabToCreate->class_name = $tab['class_name'];
                $subtabToCreate->active = 1;
                $subtabToCreate->name[(int)(Configuration::get('PS_LANG_DEFAULT'))]  = $tab['name'];
                $subtabToCreate->id_parent = Tab::getIdFromClassName($tab['parent_class_name']);
                $subtabToCreate->module = $moduleName;
                if(isset($tab['icon']))
                    $subtabToCreate->icon = $tab['icon'];
                if(!$subtabToCreate->add()){
                    return false;
                }
            }
        }
        return true;
    }

    //** @function to remove tab by class name
    public static function removeTabByClassName($tabClassName)
    {
        //** @function to remove the tab
        $idTab = Tab::getIdFromClassName($tabClassName);
        if ($idTab != 0) {
            $tab = new Tab($idTab);
            $tab->delete();
            return true;
        }
        return false;
    }

    //** @function to remove the tabs
    public static function removeModuleTabs($tabs)
    {
        foreach ($tabs as $tab){
            $idTab = Tab::getIdFromClassName($tab['class_name']);
            if ($idTab != 0) {
                try {
                    $tab = new Tab($idTab);
                    $tab->delete();
                } catch (Exception $e){
                    return false;
                }
            }
        }
        return true;
    }

    public static function installSql(array $queries)
    {
        foreach ($queries as $query){
            if(!Db::getInstance()->execute($query))
                return false;
        }
        return true;
    }

    public static function uninstallsql(array $queries) {
        foreach ($queries as $table=>$query){
            if(!Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . $table .'`'))
                return false;
        }
        return true;
    }
}