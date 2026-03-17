<?php

namespace App\Modules\Gatepass\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\View;
use App\Modules\Gatepass\Services\GatepassService;

class GateScanController extends Controller
{
    private GatepassService $service;

    public function __construct()
    {
        $this->service = new GatepassService();
    }

    private function user(): array
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        return $_SESSION['user'];
    }

    /**
     * Scan QR page
     */
    public function index()
    {
        return View::render(
            'Gatepass::scan',
            ['title' => 'Scan Gatepass'],
            'app'
        );
    }

    /**
     * Handle QR submission
     */
    public function process(Request $request)
    {
        $user = $this->user();

        $code = trim($request->input('gatepass_number', ''));

        if ($code === '') {
            return View::render(
                'Gatepass::scan',
                [
                    'title' => 'Scan Gatepass',
                    'error' => 'Invalid QR code.'
                ],
                'app'
            );
        }

        try {

            $gatepass = $this->service->findByNumber(
                $user['tenant_id'],
                $code
            );

            if (!$gatepass) {
                throw new \Exception('Gatepass not found.');
            }

            // Defensive access
            $actualIn      = $gatepass['actual_in'] ?? null;
            $actualOut     = $gatepass['actual_out'] ?? null;
            $isReturnable  = (int)($gatepass['is_returnable'] ?? 0);
            $statusId      = $gatepass['status_id'] ?? null;

            /*
            |--------------------------------------------------------------------------
            | CHECK IN
            |--------------------------------------------------------------------------
            */

            if (!$actualIn) {

                $this->service->checkIn(
                    $user['tenant_id'],
                    (int)$gatepass['id'],
                    $user['id']
                );

                return View::render(
                    'Gatepass::scan_result',
                    [
                        'title'   => 'Scan Result',
                        'message' => 'Checked in successfully.'
                    ],
                    'app'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | RETURN (if returnable and already checked in)
            |--------------------------------------------------------------------------
            */

            if ($isReturnable && $actualIn && !$actualOut) {

                $this->service->markReturned(
                    $user['tenant_id'],
                    (int)$gatepass['id']
                );

                return View::render(
                    'Gatepass::scan_result',
                    [
                        'title'   => 'Scan Result',
                        'message' => 'Item returned successfully.'
                    ],
                    'app'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | CHECK OUT
            |--------------------------------------------------------------------------
            */

            if (!$actualOut) {

                $this->service->checkOut(
                    $user['tenant_id'],
                    (int)$gatepass['id'],
                    $user['id']
                );

                return View::render(
                    'Gatepass::scan_result',
                    [
                        'title'   => 'Scan Result',
                        'message' => 'Checked out successfully.'
                    ],
                    'app'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | ALREADY COMPLETED
            |--------------------------------------------------------------------------
            */

            return View::render(
                'Gatepass::scan',
                [
                    'title' => 'Scan Gatepass',
                    'error' => 'Gatepass process already completed.'
                ],
                'app'
            );

        } catch (\Throwable $e) {

            return View::render(
                'Gatepass::scan',
                [
                    'title' => 'Scan Gatepass',
                    'error' => $e->getMessage()
                ],
                'app'
            );
        }
    }
}