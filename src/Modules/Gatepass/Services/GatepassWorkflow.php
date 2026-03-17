<?php

namespace App\Modules\Gatepass\Services;

use RuntimeException;

class GatepassWorkflow
{
    public static function resolve(array $g): array
    {
        if (empty($g['type_code']) || empty($g['status_code'])) {
            throw new RuntimeException('Invalid gatepass state: missing workflow fields.');
        }

        $type       = strtoupper($g['type_code']);
        $status     = strtoupper($g['status_code']);
        $returnable = (int)($g['is_returnable'] ?? 0) === 1;

        $actions = [
            'can_checkin'  => false,
            'can_checkout' => false,
        ];

        switch ($type) {

            case 'OUT':

                if ($status === 'APPROVED' && empty($g['actual_out'])) {
                    $actions['can_checkout'] = true;
                }

                if (
                    $status === 'CHECKED_OUT' &&
                    $returnable &&
                    empty($g['actual_in'])
                ) {
                    $actions['can_checkin'] = true;
                }

                break;

            case 'IN':

                if ($status === 'APPROVED' && empty($g['actual_in'])) {
                    $actions['can_checkin'] = true;
                }

                if ($status === 'CHECKED_IN' && empty($g['actual_out'])) {
                    $actions['can_checkout'] = true;
                }

                break;

            default:
                throw new RuntimeException("Unknown gatepass type: {$type}");
        }

        return $actions;
    }
}