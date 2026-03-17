<?php

namespace App\Modules\Settings\Controllers;

use App\Core\View;

class SettingsController
{
    public function index()
    {
        return View::render('Settings::dashboard', [
            'title' => 'Settings'
        ], 'app');
    }
}