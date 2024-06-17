<?php

class AdminTribufaqQuestionController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->context = Context::getContext();
    }
}
