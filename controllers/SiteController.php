<?php

namespace App\controllers;

class SiteController
{
    public function actionIndex()
    {
        require_once ROOT.'/';  // path to view
        return true;
    }
}
