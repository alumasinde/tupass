<?php

namespace App\Modules\Gatepass\Controllers;

use App\Core\DB;
use App\Core\View;
use App\Core\Request;
use App\Core\Controller;
use App\Core\Response;
use App\Core\Permission;
use App\Modules\Gatepass\Services\GatepassService;
use App\Modules\Gatepass\Policies\GatepassPolicy;
use App\Modules\Gatepass\Repositories\GatepassTypeRepository;
use App\Modules\Gatepass\DTOs\GatepassDTO;
use InvalidArgumentException;

class GatepassController extends Controller
{
    private GatepassService $service;
    private GatepassPolicy $policy;
    private GatepassTypeRepository $typeRepo;

    public function __construct()
    {
        $this->service  = new GatepassService();

        $permission     = new Permission(DB::connect());
        $this->policy   = new GatepassPolicy($permission);

        $this->typeRepo = new GatepassTypeRepository();
    }

    private function user(): array
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        return $_SESSION['user'];
    }

    private function findGatepassOrFail(int $tenantId, $id): array
    {
        $id = (int)$id;

        if ($id <= 0) {
            Response::abort(400, 'Invalid gatepass ID.');
        }

        $gatepass = $this->service->find($tenantId, $id);

        if (!$gatepass) {
            Response::abort(404, 'Gatepass not found.');
        }

        return $gatepass;
    }

    /* =========================================================
     * INDEX
     * ========================================================= */

    public function index()
{
    $user = $this->user();

$gatepasses = $this->service->list(
    $user['tenant_id'],
    $user['id'],
    $user['role']
);
    foreach ($gatepasses as &$g) {
        $actions = $this->service->getAvailableActions($g);

        $g['can_checkin']  = $actions['can_checkin'];
        $g['can_checkout'] = $actions['can_checkout'];
    }
    unset($g);

    return View::render('Gatepass::index', [
        'title'      => 'Gatepasses',
        'gatepasses' => $gatepasses,
    ], 'app');
}

    /* =========================================================
     * CREATE
     * ========================================================= */

    public function create()
    {
        $user = $this->user();

        if (!$this->policy->create()) {
            return Response::abort(403);
        }

        // Pass the user's department_id, not their user id
        $visits = $this->service->getVisitsForTenant(
            $user['tenant_id'],
            $user['department_id'] ?? null
        );

        $types = $this->typeRepo->findAllByTenant($user['tenant_id']);

        return View::render('Gatepass::create', [
            'title'  => 'Create Gatepass',
            'visits' => $visits,
            'types'  => $types,
        ], 'app');
    }

    public function store(Request $request)
    {
        $user = $this->user();

        if (!$this->policy->create()) {
            return Response::abort(403);
        }

        try {
            $dto = new GatepassDTO(
                tenant_id:            $user['tenant_id'],
                visit_id:             (int)$request->input('visit_id'),
                gatepass_type_id:     (int)$request->input('gatepass_type_id'),
                purpose:              trim($request->input('purpose', '')),
                is_returnable:        (bool)$request->input('is_returnable'),
                expected_return_date: $request->input('expected_return_date'),
                needs_approval:       (bool)$request->input('needs_approval'),
                created_by:           $user['id'],
                items:                $request->input('items', [])
            );

            $this->service->create($dto);

            $_SESSION['flash'] = [
                'message' => 'Gatepass created successfully.',
                'type'    => 'success',
            ];

            header('Location: /gatepasses');
            exit;

        } catch (\Throwable $e) {
            $_SESSION['flash'] = [
                'message' => $e->getMessage(),
                'type'    => 'danger',
            ];

            header('Location: /gatepasses/create');
            exit;
        }
    }

    /* =========================================================
     * SHOW
     * ========================================================= */

    public function show(Request $request, $id)
    {
        $user = $this->user();

        $gatepass = $this->findGatepassOrFail($user['tenant_id'], $id);

        return View::render('Gatepass::show', [
            'title'    => 'View Gatepass',
            'gatepass' => $gatepass,
            'items'    => $gatepass['items'] ?? [],
        ], 'app');
    }

    /* =========================================================
     * EDIT / UPDATE
     * ========================================================= */

    public function edit(Request $request, $id)
    {
        $user = $this->user();

        $gatepass = $this->findGatepassOrFail($user['tenant_id'], $id);

        if (!$this->policy->update($user, $gatepass)) {
            return Response::abort(403);

        }

        return View::render('Gatepass::edit', [
            'title'    => 'Edit Gatepass',
            'gatepass' => $gatepass,
            'items'    => $gatepass['items'] ?? [],
        ], 'app');
    }

    public function update(Request $request, $id)
    {
        $user = $this->user();

        $gatepass = $this->findGatepassOrFail($user['tenant_id'], $id);

        if (!$this->policy->update($user, $gatepass)) {
            return Response::abort(403);
        }

        try {
            $dto = new GatepassDTO(
                tenant_id:            $user['tenant_id'],
                visit_id:             (int)$request->input('visit_id'),
                gatepass_type_id:     (int)$request->input('gatepass_type_id'),
                purpose:              trim($request->input('purpose', '')),
                is_returnable:        (bool)$request->input('is_returnable'),
                expected_return_date: $request->input('expected_return_date'),
                needs_approval:       (bool)$request->input('needs_approval'),
                created_by:           $gatepass['created_by'], // preserve original creator
                items:                $request->input('items', [])
            );

            $this->service->update($user['tenant_id'], (int)$id, $dto);

            header('Location: /gatepasses');
            exit;

        } catch (InvalidArgumentException $e) {
            return View::render('Gatepass::edit', [
                'title'    => 'Edit Gatepass',
                'error'    => $e->getMessage(),
                'gatepass' => $gatepass,
                'items'    => $gatepass['items'] ?? [],
            ], 'app');
        }
    }


    /* =========================================================
     * CHECK IN / CHECK OUT
     * ========================================================= */

    public function checkIn(Request $request, int $id)
{
    $user = $this->user();

    try {
        $this->service->checkIn(
            $user['tenant_id'],
            $id,
            $user['id']
        );

        $_SESSION['flash'] = [
            'message' => 'Gatepass checked in successfully.',
            'type'    => 'success'
        ];

    } catch (\Throwable $e) {

        // If business rule fails → 403
        if ($e->getMessage() === 'Check-in not allowed in current state.') {
            return Response::abort(403, $e->getMessage());
        }

        $_SESSION['flash'] = [
            'message' => $e->getMessage(),
            'type'    => 'danger'
        ];
    }

    header('Location: /gatepasses');
    exit;
}

    public function checkOut(Request $request, int $id)
{
    $user = $this->user();

    try {
        $this->service->checkOut(
            $user['tenant_id'],
            $id,
            $user['id']
        );

        $_SESSION['flash'] = [
            'message' => 'Gatepass checked out successfully.',
            'type'    => 'success'
        ];

    } catch (\Throwable $e) {

        if ($e->getMessage() === 'Checkout not allowed in current state.') {
            return Response::abort(403, $e->getMessage());
        }

        $_SESSION['flash'] = [
            'message' => $e->getMessage(),
            'type'    => 'danger'
        ];
    }

    header('Location: /gatepasses');
    exit;
}

public function delete(Request $request, int $id)
    {
        $user = $this->user();

        if (!$this->policy->delete()) {
            return Response::abort(403);
        }

        try {
            $this->service->delete($user['tenant_id'], $id);

            $_SESSION['flash'] = [
                'message' => 'Gatepass deleted successfully.',
                'type'    => 'success'
            ];

        } catch (\Throwable $e) {
            $_SESSION['flash'] = [
                'message' => $e->getMessage(),
                'type'    => 'danger'
            ];
        }

        header('Location: /gatepasses');
        exit;
    }
}