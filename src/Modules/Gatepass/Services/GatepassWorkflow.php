<?php

namespace App\Modules\Gatepass\Services;

use RuntimeException;


class GatepassWorkflow
{
    /**
     * Evaluate which actions are physically eligible based on the gatepass's
     * current status, timestamps, and returnability flag.
     *
     * Does NOT consider type-level allowed_actions — that is GatepassTypeService's job.
     *
     * @param  array $g  Gatepass row. Required keys: status_code.
     *                   Optional keys: actual_in, actual_out, is_returnable.
     * @return array{checkin_eligible: bool, checkout_eligible: bool}
     *
     * @throws RuntimeException if status_code is missing.
     */
    public static function eligibility(array $g): array
    {
        if (empty($g['status_code'])) {
            throw new RuntimeException('Invalid gatepass state: missing status_code.');
        }

        $status     = strtoupper($g['status_code']);
        $returnable = (int)($g['is_returnable'] ?? 0) === 1;

        return [
            'checkin_eligible'  => self::isCheckinEligible($status, $returnable, $g),
            'checkout_eligible' => self::isCheckoutEligible($status, $g),
        ];
    }


    private static function isCheckinEligible(string $status, bool $returnable, array $g): bool
    {
        // Already checked in — nothing to do
        if (!empty($g['actual_in'])) {
            return false;
        }

        // Returnable gatepass that went out and is now returning
        if ($status === 'CHECKED_OUT' && $returnable) {
            return true;
        }

        // General approved state — gatepass is cleared to enter
        if ($status === 'APPROVED') {
            return true;
        }

        return false;
    }


    private static function isCheckoutEligible(string $status, array $g): bool
    {
        // Already checked out — nothing to do
        if (!empty($g['actual_out'])) {
            return false;
        }

        // Cleared to leave
        if ($status === 'APPROVED') {
            return true;
        }

        // Has checked in and is now departing
        if ($status === 'CHECKED_IN') {
            return true;
        }

        return false;
    }
}