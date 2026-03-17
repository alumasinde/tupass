<?php

namespace App\Modules\Settings\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Core\Request;
use App\Core\Response;
use App\Core\DB;
use PDO;

class WorkflowController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DB::connect();
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /* =========================================================
     * AUTH
     * ========================================================= */

    private function user(): array
    {
        if (empty($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        return $_SESSION['user'];
    }

    private function tenantId(): int
    {
        return (int) $this->user()['tenant_id'];
    }

    /* =========================================================
     * WORKFLOW HELPERS
     * ========================================================= */

    private function findOrFail(int $tenantId, int $id): array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM workflows
            WHERE id = :id
              AND tenant_id = :tenant_id
            LIMIT 1
        ");

        $stmt->execute([
            ':id'        => $id,
            ':tenant_id' => $tenantId
        ]);

        $workflow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$workflow) {
            Response::abort(404, 'Workflow not found.');
        }

        return $workflow;
    }

    /* =========================================================
     * INDEX
     * ========================================================= */

    public function index()
    {
        $tenantId = $this->tenantId();

        $stmt = $this->db->prepare("
            SELECT w.*,
                   (
                       SELECT COUNT(*)
                       FROM workflow_steps ws
                       WHERE ws.workflow_id = w.id
                         AND ws.tenant_id = w.tenant_id
                   ) AS step_count
            FROM workflows w
            WHERE w.tenant_id = :tenant_id
            ORDER BY w.created_at DESC
        ");

        $stmt->execute([':tenant_id' => $tenantId]);

        return View::render('Settings::Workflows/index', [
            'title'     => 'Workflows',
            'workflows' => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ], 'app');
    }

    /* =========================================================
     * CREATE / STORE
     * ========================================================= */

    public function create()
    {
        return View::render('Settings::Workflows/create', [
            'title' => 'Create Workflow'
        ], 'app');
    }

    public function store(Request $request)
    {
        $tenantId   = $this->tenantId();
        $name       = trim($request->input('name'));
        $description = trim($request->input('description', ''));

        if ($name === '') {
            Response::abort(422, 'Workflow name is required.');
        }

        $stmt = $this->db->prepare("
            INSERT INTO workflows
                (tenant_id, name, description, is_active, created_at)
            VALUES
                (:tenant_id, :name, :description, 1, NOW())
        ");

        $stmt->execute([
            ':tenant_id'  => $tenantId,
            ':name'       => $name,
            ':description'=> $description
        ]);

        header('Location: /settings/workflows');
        exit;
    }

    /* =========================================================
     * EDIT / UPDATE
     * ========================================================= */

    public function edit(Request $request, int $id)
    {
        $workflow = $this->findOrFail($this->tenantId(), $id);

        return View::render('Settings::Workflows/edit', [
            'title'    => 'Edit Workflow',
            'workflow' => $workflow
        ], 'app');
    }

    public function update(Request $request, int $id)
    {
        $tenantId = $this->tenantId();
        $this->findOrFail($tenantId, $id);

        $name        = trim($request->input('name'));
        $description = trim($request->input('description', ''));
        $isActive    = $request->input('is_active') ? 1 : 0;

        if ($name === '') {
            Response::abort(422, 'Workflow name is required.');
        }

        $stmt = $this->db->prepare("
            UPDATE workflows
            SET name        = :name,
                description = :description,
                is_active   = :is_active
            WHERE id = :id
              AND tenant_id = :tenant_id
        ");

        $stmt->execute([
            ':name'       => $name,
            ':description'=> $description,
            ':is_active'  => $isActive,
            ':id'         => $id,
            ':tenant_id'  => $tenantId
        ]);

        header('Location: /settings/workflows');
        exit;
    }

    /* =========================================================
     * STEPS
     * ========================================================= */

    public function steps(Request $request, int $id)
    {
        $tenantId = $this->tenantId();
        $workflow = $this->findOrFail($tenantId, $id);

        $stmt = $this->db->prepare("
            SELECT ws.*, r.name AS role_name
            FROM workflow_steps ws
            JOIN roles r
              ON r.id = ws.role_id
             AND r.tenant_id = ws.tenant_id
            WHERE ws.workflow_id = :workflow_id
              AND ws.tenant_id = :tenant_id
            ORDER BY ws.step_order ASC
        ");

        $stmt->execute([
            ':workflow_id' => $id,
            ':tenant_id'   => $tenantId
        ]);

        $steps = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $roleStmt = $this->db->prepare("
            SELECT id, name
            FROM roles
            WHERE tenant_id = :tenant_id
            ORDER BY name ASC
        ");

        $roleStmt->execute([':tenant_id' => $tenantId]);
        $roles = $roleStmt->fetchAll(PDO::FETCH_ASSOC);

        return View::render('Settings::Workflows/steps', [
            'title'    => 'Workflow Steps',
            'workflow' => $workflow,
            'steps'    => $steps,
            'roles'    => $roles
        ], 'app');
    }

    public function storeStep(Request $request, int $id)
    {
        $tenantId = $this->tenantId();
        $this->findOrFail($tenantId, $id);

        $stepOrder = (int) $request->input('step_order');
        $stepName  = trim($request->input('step_name'));
        $roleId    = (int) $request->input('role_id');

        if ($stepOrder <= 0 || $stepName === '' || $roleId <= 0) {
            Response::abort(422, 'All step fields are required.');
        }

        // Validate role ownership
        $roleStmt = $this->db->prepare("
            SELECT id
            FROM roles
            WHERE id = :id
              AND tenant_id = :tenant_id
            LIMIT 1
        ");

        $roleStmt->execute([
            ':id'        => $roleId,
            ':tenant_id' => $tenantId
        ]);

        if (!$roleStmt->fetch()) {
            Response::abort(403, 'Invalid role for this tenant.');
        }

        $stmt = $this->db->prepare("
            INSERT INTO workflow_steps
                (tenant_id, workflow_id, step_order, name, role_id)
            VALUES
                (:tenant_id, :workflow_id, :step_order, :name, :role_id)
        ");

        $stmt->execute([
            ':tenant_id'  => $tenantId,
            ':workflow_id'=> $id,
            ':step_order' => $stepOrder,
            ':name'       => $stepName,
            ':role_id'    => $roleId
        ]);

        header("Location: /settings/workflows/{$id}/steps");
        exit;
    }

    /* =========================================================
     * ASSIGN WORKFLOW TO GATEPASS TYPE (PIVOT TABLE)
     * ========================================================= */

    public function assign(Request $request, int $id)
    {
        $tenantId = $this->tenantId();
        $workflow = $this->findOrFail($tenantId, $id);

        $stmt = $this->db->prepare("
            SELECT id, name
            FROM gatepass_types
            WHERE tenant_id = :tenant_id
            ORDER BY name ASC
        ");

        $stmt->execute([':tenant_id' => $tenantId]);

        return View::render('Settings::Workflows/assign', [
            'title'         => 'Assign Workflow',
            'workflow'      => $workflow,
            'gatepassTypes' => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ], 'app');
    }

    public function storeAssignment(Request $request, int $id)
    {
        $tenantId = $this->tenantId();
        $this->findOrFail($tenantId, $id);

        $gatepassTypeId = (int) $request->input('gatepass_type_id');

        if ($gatepassTypeId <= 0) {
            Response::abort(422, 'Gatepass type is required.');
        }

        // Validate type ownership
        $typeStmt = $this->db->prepare("
            SELECT id
            FROM gatepass_types
            WHERE id = :id
              AND tenant_id = :tenant_id
            LIMIT 1
        ");

        $typeStmt->execute([
            ':id'        => $gatepassTypeId,
            ':tenant_id' => $tenantId
        ]);

        if (!$typeStmt->fetch()) {
            Response::abort(403, 'Invalid gatepass type.');
        }

        // Insert into pivot (safe due to UNIQUE constraint)
        $stmt = $this->db->prepare("
            INSERT IGNORE INTO workflow_gatepass_type
                (tenant_id, workflow_id, gatepass_type_id, created_at)
            VALUES
                (:tenant_id, :workflow_id, :gatepass_type_id, NOW())
        ");

        $stmt->execute([
            ':tenant_id'       => $tenantId,
            ':workflow_id'     => $id,
            ':gatepass_type_id'=> $gatepassTypeId
        ]);

        header('Location: /settings/workflows');
        exit;
    }
}