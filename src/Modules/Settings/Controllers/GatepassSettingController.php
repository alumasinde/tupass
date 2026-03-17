<?php

namespace App\Modules\Settings\Controllers;

use App\Core\Request;
use App\Core\View;
use App\Modules\Settings\Services\TenantSettingService;

class GatepassSettingController
{
    /**
     * Show numbering settings page
     */
    public function index()
    {
        $settings = new TenantSettingService();

        $config = $settings->get('gatepass_numbering', [
            'prefix'        => 'GP',
            'include_year'  => true,
            'include_month' => false,
            'padding'       => 4,
            'reset_yearly'  => true,
            'current_year'  => date('Y'),
            'sequence'      => 1,
        ]);

        return View::render('Settings::gatepass-numbering', [
            'title'  => 'Gatepass Numbering Settings',
            'config' => $config
        ], 'app');
    }

    /**
     * Update numbering settings
     */
    public function update(Request $request)
    {
        $settings = new TenantSettingService();

        $config = [
            'prefix'        => trim($request->input('prefix') ?? 'GP'),
            'include_year'  => $request->input('include_year') ? true : false,
            'include_month' => $request->input('include_month') ? true : false,
            'padding'       => max(1, (int)($request->input('padding') ?? 4)),
            'reset_yearly'  => $request->input('reset_yearly') ? true : false,
            'current_year'  => date('Y'),
            'sequence'      => max(1, (int)($request->input('sequence') ?? 1)),
        ];

        $settings->set('gatepass_numbering', $config);

        header("Location: /settings/gatepass-numbering");
        exit;
    }
}