<?php

// ── Helpers ──────────────────────────────────────────────────────
function statusLabel(string $code): string {
    return match(strtoupper($code)) {
        'PENDING'      => 'Pending',
        'APPROVED'     => 'Approved',
        'REJECTED'     => 'Rejected',
        'CHECKED_IN'   => 'Checked In',
        'CHECKED_OUT'  => 'Checked Out',
        'CANCELLED'    => 'Cancelled',
        default        => ucwords(strtolower(str_replace('_', ' ', $code))),
    };
}

function statusClass(string $code): string {
    return match(strtoupper($code)) {
        'PENDING'                => 'gs-status--pending',
        'APPROVED'               => 'gs-status--approved',
        'REJECTED', 'CANCELLED'  => 'gs-status--rejected',
        'CHECKED_IN'             => 'gs-status--in',
        'CHECKED_OUT'            => 'gs-status--out',
        default                  => 'gs-status--neutral',
    };
}

$statusCode = strtoupper($gatepass['status_code'] ?? '');
$canCheckin  = (bool)($actions['can_checkin']  ?? false);
$canCheckout = (bool)($actions['can_checkout'] ?? false);
$hasAnyAction = $canCheckin || $canCheckout;
?>

<div class="gs-page">

    <!-- ── Breadcrumb ───────────────────────────────────────────── -->
    <nav class="gs-breadcrumb" aria-label="Breadcrumb">
        <a href="/gatepasses" class="gs-breadcrumb__link">Gatepasses</a>
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M4.5 2.5l3 3.5-3 3.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
        <span class="gs-breadcrumb__current">GP-<?= (int)$gatepass['id'] ?></span>
    </nav>

    <div class="gs-layout">

        <!-- ── Main column ──────────────────────────────────────── -->
        <div class="gs-main">

            <!-- Gatepass header card -->
            <div class="gs-card gs-card--header">
                <div class="gs-card__row gs-card__row--spread">
                    <div>
                        <div class="gs-ref">GP-<?= (int)$gatepass['id'] ?></div>
                        <h1 class="gs-card__title"><?= htmlspecialchars($visitor['name'] ?? 'Unknown Visitor') ?></h1>
                        <p class="gs-card__sub"><?= htmlspecialchars($visitor['company'] ?? '') ?></p>
                    </div>
                    <span class="gs-status <?= statusClass($statusCode) ?>">
                        <?= statusLabel($statusCode) ?>
                    </span>
                </div>

                <!-- Type + Returnable pills -->
                <div class="gs-pill-row">
                    <span class="gs-pill">
                        <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><rect x="2" y="2" width="9" height="9" rx="2" stroke="currentColor" stroke-width="1.2"/></svg>
                        Type: <?= htmlspecialchars($type['name'] ?? '—') ?>
                    </span>
                    <?php if ((int)($gatepass['is_returnable'] ?? 0) === 1): ?>
                        <span class="gs-pill gs-pill--blue">
                            <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M9.5 4.5A4 4 0 1 0 10 8" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/><path d="M10 4.5H7.5V7" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Returnable
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Time details card -->
            <div class="gs-card">
                <h2 class="gs-card__section-title">Time Details</h2>
                <div class="gs-grid-2">
                    <div class="gs-field">
                        <span class="gs-field__label">Scheduled In</span>
                        <span class="gs-field__value"><?= $gatepass['expected_in']  ? date('D, d M Y · H:i', strtotime($gatepass['expected_in']))  : '—' ?></span>
                    </div>
                    <div class="gs-field">
                        <span class="gs-field__label">Scheduled Out</span>
                        <span class="gs-field__value"><?= $gatepass['expected_out'] ? date('D, d M Y · H:i', strtotime($gatepass['expected_out'])) : '—' ?></span>
                    </div>
                    <div class="gs-field">
                        <span class="gs-field__label">Actual Check-in</span>
                        <span class="gs-field__value <?= empty($gatepass['actual_in']) ? 'gs-field__value--muted' : 'gs-field__value--done' ?>">
                            <?= !empty($gatepass['actual_in']) ? date('D, d M Y · H:i', strtotime($gatepass['actual_in'])) : 'Not yet recorded' ?>
                        </span>
                    </div>
                    <div class="gs-field">
                        <span class="gs-field__label">Actual Check-out</span>
                        <span class="gs-field__value <?= empty($gatepass['actual_out']) ? 'gs-field__value--muted' : 'gs-field__value--done' ?>">
                            <?= !empty($gatepass['actual_out']) ? date('D, d M Y · H:i', strtotime($gatepass['actual_out'])) : 'Not yet recorded' ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Notes / purpose -->
            <?php if (!empty($gatepass['purpose'])): ?>
                <div class="gs-card">
                    <h2 class="gs-card__section-title">Purpose / Notes</h2>
                    <p class="gs-notes"><?= nl2br(htmlspecialchars($gatepass['purpose'])) ?></p>
                </div>
            <?php endif; ?>

            <!-- Approval history -->
            <?php if (!empty($approvals)): ?>
                <div class="gs-card">
                    <h2 class="gs-card__section-title">Approval History</h2>
                    <ol class="gs-timeline" reversed>
                        <?php foreach ($approvals as $approval): ?>
                            <li class="gs-timeline__item">
                                <span class="gs-timeline__dot gs-timeline__dot--<?= strtolower(htmlspecialchars($approval['decision'] ?? 'pending')) ?>"></span>
                                <div class="gs-timeline__body">
                                    <div class="gs-timeline__top">
                                        <strong><?= htmlspecialchars($approval['approver_name'] ?? 'System') ?></strong>
                                        <span class="gs-timeline__decision gs-timeline__decision--<?= strtolower(htmlspecialchars($approval['decision'] ?? 'pending')) ?>">
                                            <?= htmlspecialchars(ucfirst(strtolower($approval['decision'] ?? 'Pending'))) ?>
                                        </span>
                                    </div>
                                    <time class="gs-timeline__time"><?= date('d M Y, H:i', strtotime($approval['created_at'])) ?></time>
                                    <?php if (!empty($approval['remarks'])): ?>
                                        <p class="gs-timeline__remarks"><?= htmlspecialchars($approval['remarks']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            <?php endif; ?>

        </div><!-- /gs-main -->

        <!-- ── Sidebar: Actions ─────────────────────────────────── -->
        <aside class="gs-sidebar">

            <?php if ($hasAnyAction): ?>
                <div class="gs-card gs-card--actions">
                    <h2 class="gs-card__section-title">Available Actions</h2>
                    <p class="gs-actions__hint">
                        Actions shown are permitted by the
                        <strong><?= htmlspecialchars($type['name'] ?? 'this') ?></strong> type
                        and eligible based on current workflow state.
                    </p>

                    <div class="gs-action-group">

                        <?php if ($canCheckin): ?>
                            <button
                                class="gs-action-btn gs-action-btn--checkin"
                                onclick="confirmAction('checkin')"
                            >
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M9 3v7.5M9 10.5l-3-3M9 10.5l3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 15h10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                Record Check-in
                            </button>
                        <?php endif; ?>

                        <?php if ($canCheckout): ?>
                            <button
                                class="gs-action-btn gs-action-btn--checkout"
                                onclick="confirmAction('checkout')"
                            >
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M9 15V7.5M9 7.5l-3 3M9 7.5l3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 3h10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                Record Check-out
                            </button>
                        <?php endif; ?>

                    </div>
                </div>
            <?php else: ?>
                <!-- No actions available - explain why clearly -->
                <div class="gs-card gs-card--no-actions">
                    <div class="gs-no-actions">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none"><circle cx="16" cy="16" r="13" stroke="currentColor" stroke-width="1.5"/><path d="M16 10v6M16 20h.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        <p class="gs-no-actions__title">No actions available</p>
                        <p class="gs-no-actions__reason">
                            <?php
                            // Explain clearly which gate is blocking action
                            $typeAllowed = json_decode($type['allowed_actions'] ?? '{}', true) ?: [];
                            $typeAllowsCheckin  = (bool)($typeAllowed['checkin']  ?? false);
                            $typeAllowsCheckout = (bool)($typeAllowed['checkout'] ?? false);

                            if (!$typeAllowsCheckin && !$typeAllowsCheckout):
                            ?>
                                Neither check-in nor check-out is enabled for the
                                <strong><?= htmlspecialchars($type['name'] ?? 'this') ?></strong> type.
                                Update the type configuration in Settings to enable actions.
                            <?php elseif (in_array($statusCode, ['REJECTED', 'CANCELLED'])): ?>
                                This gatepass has been <strong><?= statusLabel($statusCode) ?></strong>
                                and no further actions are permitted.
                            <?php elseif ($statusCode === 'PENDING'): ?>
                                This gatepass is <strong>pending approval</strong> and cannot be acted on yet.
                            <?php else: ?>
                                All permitted actions for this gatepass have already been completed
                                based on its current status.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Meta info card -->
            <div class="gs-card">
                <h2 class="gs-card__section-title">Meta</h2>
                <dl class="gs-meta">
                    <div class="gs-meta__row">
                        <dt>Created</dt>
                        <dd><?= date('d M Y, H:i', strtotime($gatepass['created_at'])) ?></dd>
                    </div>
                    <div class="gs-meta__row">
                        <dt>Last Updated</dt>
                        <dd><?= date('d M Y, H:i', strtotime($gatepass['updated_at'] ?? $gatepass['created_at'])) ?></dd>
                    </div>
                    <div class="gs-meta__row">
                        <dt>Created By</dt>
                        <dd><?= htmlspecialchars($gatepass['created_by_name'] ?? '—') ?></dd>
                    </div>
                </dl>
            </div>

        </aside><!-- /gs-sidebar -->

    </div><!-- /gs-layout -->
</div>

<!-- ── Confirm Action Modal ──────────────────────────────────────── -->
<div class="gs-overlay" id="gsOverlay" role="dialog" aria-modal="true" aria-labelledby="gsModalTitle" hidden>
    <div class="gs-modal">
        <div class="gs-modal__icon" id="gsModalIcon"></div>
        <h2 class="gs-modal__title" id="gsModalTitle"></h2>
        <p class="gs-modal__body" id="gsModalBody"></p>
        <div class="gs-modal__footer">
            <button class="gt-btn gt-btn--ghost" onclick="closeActionModal()">Cancel</button>
            <button class="gt-btn" id="gsConfirmBtn" onclick="submitAction()">Confirm</button>
        </div>
    </div>
</div>

<!-- ── Styles ────────────────────────────────────────────────────── -->
<style>
/* ── Layout ── */
.gs-page   { max-width: 1060px; margin: 0 auto; padding: 2rem 1.5rem 4rem; }
.gs-layout { display: grid; grid-template-columns: 1fr 300px; gap: 1.25rem; align-items: start; margin-top: 1.25rem; }
@media (max-width: 720px) { .gs-layout { grid-template-columns: 1fr; } .gs-sidebar { order: -1; } }

/* ── Breadcrumb ── */
.gs-breadcrumb { display: flex; align-items: center; gap: .375rem; font-size: .8125rem; color: var(--color-text-muted, #6b7280); margin-bottom: .75rem; }
.gs-breadcrumb__link { color: var(--color-accent, #2563eb); text-decoration: none; }
.gs-breadcrumb__link:hover { text-decoration: underline; }

/* ── Cards ── */
.gs-card { background: var(--color-surface, #fff); border: 1px solid var(--color-border, #e5e7eb); border-radius: 12px; padding: 1.25rem 1.375rem; margin-bottom: 1rem; }
.gs-card--header { padding-bottom: 1rem; }
.gs-card__row--spread { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; }
.gs-ref   { font-size: .75rem; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: var(--color-text-muted, #9ca3af); margin-bottom: .25rem; }
.gs-card__title { font-size: 1.25rem; font-weight: 650; color: var(--color-text-primary, #111); margin: 0 0 .1rem; letter-spacing: -.02em; }
.gs-card__sub   { font-size: .875rem; color: var(--color-text-muted, #6b7280); margin: 0; }
.gs-card__section-title { font-size: .8125rem; font-weight: 600; text-transform: uppercase; letter-spacing: .07em; color: var(--color-text-muted, #9ca3af); margin: 0 0 1rem; }

/* ── Status badge ── */
.gs-status { padding: .3em .75em; border-radius: 99px; font-size: .75rem; font-weight: 700; letter-spacing: .03em; display: inline-flex; align-items: center; white-space: nowrap; flex-shrink: 0; }
.gs-status--pending   { background: #fef9c3; color: #854d0e; }
.gs-status--approved  { background: #dcfce7; color: #15803d; }
.gs-status--rejected  { background: #fee2e2; color: #991b1b; }
.gs-status--in        { background: #dbeafe; color: #1e40af; }
.gs-status--out       { background: #f3e8ff; color: #6b21a8; }
.gs-status--neutral   { background: #f3f4f6; color: #374151; }

/* ── Pills ── */
.gs-pill-row { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: .875rem; }
.gs-pill { display: inline-flex; align-items: center; gap: .35rem; padding: .25em .7em; border-radius: 99px; font-size: .75rem; font-weight: 550; background: var(--color-surface-subtle, #f3f4f6); color: var(--color-text-secondary, #374151); border: 1px solid var(--color-border, #e5e7eb); }
.gs-pill--blue { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }

/* ── Field grid ── */
.gs-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
@media (max-width: 480px) { .gs-grid-2 { grid-template-columns: 1fr; } }
.gs-field__label { display: block; font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: var(--color-text-muted, #9ca3af); margin-bottom: .25rem; }
.gs-field__value { font-size: .875rem; color: var(--color-text-primary, #111); }
.gs-field__value--muted { color: var(--color-text-muted, #9ca3af); font-style: italic; }
.gs-field__value--done  { color: #15803d; font-weight: 550; }

/* ── Notes ── */
.gs-notes { margin: 0; font-size: .875rem; color: var(--color-text-secondary, #374151); line-height: 1.6; white-space: pre-wrap; }

/* ── Timeline ── */
.gs-timeline { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0; }
.gs-timeline__item { display: flex; gap: .875rem; padding-bottom: 1.25rem; position: relative; }
.gs-timeline__item:last-child { padding-bottom: 0; }
.gs-timeline__item::before { content: ''; position: absolute; left: 7px; top: 20px; bottom: 0; width: 1px; background: var(--color-border, #e5e7eb); }
.gs-timeline__item:last-child::before { display: none; }
.gs-timeline__dot { width: 15px; height: 15px; border-radius: 50%; flex-shrink: 0; margin-top: 3px; border: 2px solid; }
.gs-timeline__dot--approved { background: #dcfce7; border-color: #16a34a; }
.gs-timeline__dot--rejected { background: #fee2e2; border-color: #dc2626; }
.gs-timeline__dot--pending  { background: #fef9c3; border-color: #ca8a04; }
.gs-timeline__body { flex: 1; min-width: 0; }
.gs-timeline__top  { display: flex; align-items: center; justify-content: space-between; gap: .5rem; flex-wrap: wrap; }
.gs-timeline__top strong { font-size: .875rem; font-weight: 600; }
.gs-timeline__decision { font-size: .75rem; font-weight: 600; padding: .15em .5em; border-radius: 99px; }
.gs-timeline__decision--approved { background: #dcfce7; color: #15803d; }
.gs-timeline__decision--rejected { background: #fee2e2; color: #991b1b; }
.gs-timeline__decision--pending  { background: #fef9c3; color: #854d0e; }
.gs-timeline__time    { display: block; font-size: .75rem; color: var(--color-text-muted, #9ca3af); margin-top: .2rem; }
.gs-timeline__remarks { font-size: .8125rem; color: var(--color-text-secondary, #374151); margin: .375rem 0 0; }

/* ── Action buttons ── */
.gs-card--actions  { border-color: var(--color-accent, #2563eb); }
.gs-actions__hint  { font-size: .75rem; color: var(--color-text-muted, #6b7280); margin: -.25rem 0 1rem; line-height: 1.5; }
.gs-action-group   { display: flex; flex-direction: column; gap: .625rem; }
.gs-action-btn     { display: flex; align-items: center; gap: .625rem; width: 100%; padding: .8125rem 1rem; border: none; border-radius: 9px; font-size: .9375rem; font-weight: 600; cursor: pointer; transition: opacity .15s, transform .1s; }
.gs-action-btn:hover  { opacity: .88; }
.gs-action-btn:active { transform: scale(.98); }
.gs-action-btn--checkin  { background: #2563eb; color: #fff; }
.gs-action-btn--checkout { background: #7c3aed; color: #fff; }

/* ── No-actions card ── */
.gs-card--no-actions { border-style: dashed; }
.gs-no-actions { display: flex; flex-direction: column; align-items: center; text-align: center; gap: .5rem; padding: .5rem 0; color: var(--color-text-muted, #9ca3af); }
.gs-no-actions__title  { font-size: .875rem; font-weight: 600; color: var(--color-text-secondary, #374151); margin: 0; }
.gs-no-actions__reason { font-size: .8125rem; color: var(--color-text-muted, #6b7280); margin: 0; line-height: 1.5; }

/* ── Meta dl ── */
.gs-meta { margin: 0; display: flex; flex-direction: column; gap: .625rem; }
.gs-meta__row { display: flex; justify-content: space-between; align-items: baseline; gap: .5rem; font-size: .8125rem; }
.gs-meta__row dt { color: var(--color-text-muted, #6b7280); flex-shrink: 0; }
.gs-meta__row dd { margin: 0; color: var(--color-text-primary, #111); text-align: right; }

/* ── Confirm modal ── */
.gs-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.45); display: flex; align-items: center; justify-content: center; z-index: 200; padding: 1rem; }
.gs-overlay[hidden] { display: none; }
.gs-modal { background: var(--color-surface, #fff); border-radius: 14px; width: 100%; max-width: 360px; padding: 2rem 1.5rem 1.5rem; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,.18); animation: modalIn .18s ease; }
.gs-modal__icon  { font-size: 2rem; margin-bottom: .75rem; }
.gs-modal__title { font-size: 1.0625rem; font-weight: 650; margin: 0 0 .5rem; }
.gs-modal__body  { font-size: .875rem; color: var(--color-text-muted, #6b7280); margin: 0 0 1.5rem; line-height: 1.5; }
.gs-modal__footer { display: flex; justify-content: center; gap: .625rem; }
@keyframes modalIn { from { transform: translateY(10px) scale(.97); opacity: 0; } to { transform: none; opacity: 1; } }

/* ── Shared button styles (mirrors gatepass-types.php) ── */
.gt-btn { display: inline-flex; align-items: center; gap: .4rem; padding: .5rem .9rem; border-radius: 8px; border: none; font-size: .875rem; font-weight: 550; cursor: pointer; transition: background .15s, opacity .15s; }
.gt-btn--ghost { background: transparent; color: var(--color-text-secondary, #374151); border: 1px solid var(--color-border, #d1d5db); }
.gt-btn--ghost:hover { background: var(--color-surface-subtle, #f9fafb); }
.gt-btn--checkin  { background: #2563eb; color: #fff; }
.gt-btn--checkout { background: #7c3aed; color: #fff; }
.gt-btn:disabled  { opacity: .55; cursor: not-allowed; }
</style>

<!-- ── Script ────────────────────────────────────────────────────── -->
<script>
(function () {
    'use strict';

    const GATEPASS_ID = <?= (int)$gatepass['id'] ?>;
    let pendingAction = null;

    const CONFIG = {
        checkin: {
            icon:    '⬇️',
            title:   'Confirm Check-in',
            body:    'This will record the actual check-in time as now. This cannot be undone.',
            btnClass: 'gt-btn gt-btn--checkin',
            btnLabel: 'Record Check-in',
            endpoint: '/gatepasses/' + GATEPASS_ID + '/checkin',
        },
        checkout: {
            icon:    '⬆️',
            title:   'Confirm Check-out',
            body:    'This will record the actual check-out time as now. This cannot be undone.',
            btnClass: 'gt-btn gt-btn--checkout',
            btnLabel: 'Record Check-out',
            endpoint: '/gatepasses/' + GATEPASS_ID + '/checkout',
        },
    };

    window.confirmAction = function (action) {
        const cfg = CONFIG[action];
        if (!cfg) return;

        pendingAction = action;

        document.getElementById('gsModalIcon').textContent  = cfg.icon;
        document.getElementById('gsModalTitle').textContent = cfg.title;
        document.getElementById('gsModalBody').textContent  = cfg.body;

        const btn = document.getElementById('gsConfirmBtn');
        btn.className   = cfg.btnClass;
        btn.textContent = cfg.btnLabel;

        document.getElementById('gsOverlay').hidden = false;
        btn.focus();
    };

    window.closeActionModal = function () {
        document.getElementById('gsOverlay').hidden = true;
        pendingAction = null;
    };

    window.submitAction = async function () {
        if (!pendingAction) return;

        const cfg = CONFIG[pendingAction];
        const btn = document.getElementById('gsConfirmBtn');

        btn.disabled    = true;
        btn.textContent = 'Recording…';

        try {
            const res = await apiPut(cfg.endpoint, { id: GATEPASS_ID });

            if (!res.ok) {
                const data = await res.json().catch(() => ({}));
                throw new Error(data.message || 'Action failed. Please try again.');
            }

            // Hard reload so all derived state (status, timestamps, actions) is fresh from server
            window.location.reload();

        } catch (err) {
            showToast(err.message, 'error');
            closeActionModal();
        } finally {
            btn.disabled    = false;
            btn.textContent = cfg.btnLabel;
        }
    };

    // Backdrop / Escape close
    document.getElementById('gsOverlay').addEventListener('click', function (e) {
        if (e.target === this) closeActionModal();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeActionModal();
    });
})();
</script>
