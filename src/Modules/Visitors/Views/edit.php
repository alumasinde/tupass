<?php
$details = $visitor ?? [];

if (empty($details)) {
    echo '<div class="alert alert-danger">Visitor not found.</div>';
    return;
}
?>

<div class="page-header">
    <h2>Edit Visitor</h2>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?>">
        <?= htmlspecialchars($_SESSION['flash']['message']) ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<form method="POST" action="/visitors/<?= (int)$details['id'] ?>/update" class="visitor-form">

    <div class="card">
        <div class="card-body">

            <div class="form-grid">

                <div class="form-group">
                    <label>First Name</label>
                    <input type="text"
                           name="first_name"
                           value="<?= htmlspecialchars($details['first_name'] ?? '') ?>"
                           required>
                </div>

                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text"
                           name="last_name"
                           value="<?= htmlspecialchars($details['last_name'] ?? '') ?>"
                           required>
                </div>

                <div class="form-group">
                    <label>ID Type</label>
                    <select name="id_type_id" required>
                        <?php foreach ($idTypes as $type): ?>
                            <option value="<?= (int)$type['id'] ?>"
                                <?= ((int)$type['id'] === (int)($details['id_type_id'] ?? 0)) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>ID Number</label>
                    <input type="text"
                           name="id_number"
                           value="<?= htmlspecialchars($details['id_number'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Phone</label>
                    <input type="text"
                           name="phone"
                           value="<?= htmlspecialchars($details['phone'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email"
                           name="email"
                           value="<?= htmlspecialchars($details['email'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Company</label>
                    <select name="company_id">
                        <option value="">-- Select Company --</option>
                        <?php foreach ($companies as $company): ?>
                            <option value="<?= (int)$company['id'] ?>"
                                <?= ((int)$company['id'] === (int)($details['company_id'] ?? 0)) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($company['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Or Add New Company</label>
                    <input type="text"
                           name="new_company_name"
                           placeholder="Enter new company">
                </div>

            </div>

        </div>

        <div class="card-footer">
            <a href="/visitors/<?= (int)$details['id'] ?>" class="btn btn-secondary">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                Update Visitor
            </button>
        </div>
    </div>

</form>

<style>

.page-header {
    margin-bottom: 20px;
}

.page-header h2 {
    font-size: 22px;
    font-weight: 600;
}

.card {
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    overflow: hidden;
}

.card-body {
    padding: 25px;
}

.card-footer {
    padding: 15px 25px;
    background: #f9f9f9;
    text-align: right;
    border-top: 1px solid #eee;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-size: 14px;
    margin-bottom: 6px;
    font-weight: 500;
}

.form-group input,
.form-group select {
    padding: 10px 12px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
    transition: 0.2s ease;
}

.form-group input:focus,
.form-group select:focus {
    border-color: #2563eb;
    outline: none;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
}

.btn {
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    cursor: pointer;
    border: none;
    display: inline-block;
}

.btn-primary {
    background-color: #2563eb;
    color: #ffffff;
}

.btn-primary:hover {
    background-color: #1d4ed8;
}

.btn-secondary {
    background-color: #6b7280;
    color: #ffffff;
}

.btn-secondary:hover {
    background-color: #4b5563;
}

.alert {
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 20px;
    font-size: 14px;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
}

.alert-danger {
    background: #fee2e2;
    color: #991b1b;
}

.alert-warning {
    background: #fef3c7;
    color: #92400e;
}


</style>